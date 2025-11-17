<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * Allowed file extensions and their MIME types for security.
     */
    protected const ALLOWED_TYPES = [
        // Images
        'jpg' => ['image/jpeg', 'image/jpg'],
        'jpeg' => ['image/jpeg', 'image/jpg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'svg' => ['image/svg+xml'],

        // Documents
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'txt' => ['text/plain'],
        'csv' => ['text/csv', 'text/plain'],

        // Archives
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/x-rar-compressed', 'application/vnd.rar'],
        '7z' => ['application/x-7z-compressed'],

        // Code
        'json' => ['application/json'],
        'xml' => ['application/xml', 'text/xml'],
    ];

    /**
     * Maximum file size in bytes (50MB).
     */
    protected const MAX_FILE_SIZE = 52428800;

    /**
     * Upload a file and create attachment record.
     *
     * @param UploadedFile $file
     * @param string $attachableType
     * @param int $attachableId
     * @param User $uploader
     * @param string $storageDisk
     * @param string|null $knownMimeType
     * @return Attachment
     * @throws \Exception
     */
    public function uploadFile(
        UploadedFile $file,
        string $attachableType,
        int $attachableId,
        User $uploader,
        string $storageDisk = 'local',
        ?string $knownMimeType = null
    ): Attachment {
        // Validate file (pass known MIME type if available)
        $this->validateFile($file, $knownMimeType);

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(40) . '.' . $extension;

        // Determine storage path
        $directory = $this->getStorageDirectory($attachableType);
        $filePath = $directory . '/' . $fileName;

        // Calculate file hash for duplicate detection
        $hash = hash_file('sha256', $file->getRealPath());

        // Check for duplicate
        $duplicate = Attachment::where('hash', $hash)
            ->where('attachable_type', $attachableType)
            ->where('attachable_id', $attachableId)
            ->first();

        if ($duplicate) {
            throw new \Exception('This file has already been uploaded.');
        }

        // Upload file to storage (using native PHP to avoid finfo dependency)
        $diskConfig = config("filesystems.disks.{$storageDisk}");

        if ($storageDisk === 'local') {
            // For local storage, use native PHP file operations
            $fullPath = storage_path('app/private/' . $filePath);
            $directoryPath = dirname($fullPath);

            // Create directory if it doesn't exist
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            // Copy file to destination
            copy($file->getRealPath(), $fullPath);
        } else {
            // For cloud storage, we still need to use Storage facade
            // Set MIME type explicitly to avoid detection
            Storage::disk($storageDisk)->put(
                $filePath,
                file_get_contents($file->getRealPath()),
                [
                    'visibility' => 'private',
                    'mimetype' => $knownMimeType ?? 'application/octet-stream',
                ]
            );
        }

        // Create attachment record
        $attachment = Attachment::create([
            'attachable_type' => $attachableType,
            'attachable_id' => $attachableId,
            'uploaded_by' => $uploader->id,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $knownMimeType ?? $file->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'storage_disk' => $storageDisk,
            'hash' => $hash,
        ]);

        return $attachment;
    }

    /**
     * Validate uploaded file.
     *
     * @param UploadedFile $file
     * @param string|null $knownMimeType
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file, ?string $knownMimeType = null): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 50MB.');
        }

        // Get file extension
        $extension = strtolower($file->getClientOriginalExtension());

        // Check if extension is allowed
        if (!isset(self::ALLOWED_TYPES[$extension])) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', array_keys(self::ALLOWED_TYPES)));
        }

        // Verify MIME type matches extension (use known MIME type if provided)
        $mimeType = $knownMimeType ?? $file->getClientMimeType() ?? 'application/octet-stream';
        $allowedMimes = self::ALLOWED_TYPES[$extension];

        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception('File MIME type does not match extension. Possible security threat detected.');
        }

        // Additional security checks
        $this->performSecurityChecks($file);
    }

    /**
     * Perform additional security checks on the file.
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function performSecurityChecks(UploadedFile $file): void
    {
        // Check for executable content in non-executable files
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'exe', 'sh', 'bat', 'cmd'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, $dangerousExtensions)) {
            throw new \Exception('Executable files are not allowed.');
        }

        // Check file content for PHP tags (basic check)
        $content = file_get_contents($file->getRealPath());
        if (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false) {
            throw new \Exception('File contains potentially malicious content.');
        }

        // For SVG files, check for script tags
        if ($extension === 'svg') {
            if (preg_match('/<script[\s\S]*?>[\s\S]*?<\/script>/i', $content)) {
                throw new \Exception('SVG file contains script tags and is not allowed.');
            }
        }

        // TODO: Implement virus scanning using ClamAV or similar
        // This would require installing and configuring a virus scanner
        // $this->scanForViruses($file);
    }

    /**
     * Get storage directory based on attachable type.
     *
     * @param string $attachableType
     * @return string
     */
    protected function getStorageDirectory(string $attachableType): string
    {
        $typeMap = [
            'App\\Models\\Project' => 'attachments/projects',
            'App\\Models\\Task' => 'attachments/tasks',
            'App\\Models\\TaskComment' => 'attachments/comments',
            'App\\Models\\Discussion' => 'attachments/discussions',
            'App\\Models\\DiscussionComment' => 'attachments/discussion-comments',
        ];

        return $typeMap[$attachableType] ?? 'attachments/other';
    }

    /**
     * Delete an attachment.
     *
     * @param Attachment $attachment
     * @param User $user
     * @throws \Exception
     */
    public function deleteAttachment(Attachment $attachment, User $user): void
    {
        // Check authorization - only uploader, owner, or admin can delete
        if ($attachment->uploaded_by !== $user->id && !$user->isOwnerOrAdmin()) {
            throw new \Exception('You do not have permission to delete this attachment.');
        }

        // Delete the attachment (file deletion is handled in model boot method)
        $attachment->delete();
    }

    /**
     * Get file content for download.
     *
     * @param Attachment $attachment
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function downloadAttachment(Attachment $attachment, User $user)
    {
        // Check authorization
        $this->authorizeAccess($attachment, $user);

        if ($attachment->storage_disk === 'local') {
            // For local storage, use native PHP (avoid finfo dependency)
            $fullPath = storage_path('app/private/' . $attachment->file_path);

            // Check if file exists
            if (!file_exists($fullPath)) {
                throw new \Exception('File not found.');
            }

            // Return download response using native PHP
            return response()->download($fullPath, $attachment->original_name, [
                'Content-Type' => $attachment->mime_type,
            ]);
        } else {
            // For cloud storage, use Storage facade
            // Check if file exists
            if (!Storage::disk($attachment->storage_disk)->exists($attachment->file_path)) {
                throw new \Exception('File not found.');
            }

            // Return file download response
            return Storage::disk($attachment->storage_disk)->download(
                $attachment->file_path,
                $attachment->original_name
            );
        }
    }

    /**
     * Check if user is authorized to access attachment.
     *
     * @param Attachment $attachment
     * @param User $user
     * @throws \Exception
     */
    protected function authorizeAccess(Attachment $attachment, User $user): void
    {
        $attachable = $attachment->attachable;

        // Different authorization logic based on attachable type
        switch (get_class($attachable)) {
            case 'App\\Models\\Project':
                // User must be a member of the project
                if (!$attachable->users()->where('user_id', $user->id)->exists()) {
                    throw new \Exception('You do not have permission to access this file.');
                }
                break;

            case 'App\\Models\\Task':
                // User must be assigned to task, watching it, or be a project member
                $project = $attachable->project;
                $canAccess = $attachable->assignee_id === $user->id ||
                             $attachable->watchers()->where('users.id', $user->id)->exists() ||
                             $project->users()->where('user_id', $user->id)->exists();

                if (!$canAccess && $user->isGuest()) {
                    throw new \Exception('You do not have permission to access this file.');
                }
                break;

            case 'App\\Models\\Discussion':
                // User must be in the same company
                if ($attachable->company_id !== $user->companies->first()?->id) {
                    throw new \Exception('You do not have permission to access this file.');
                }
                break;

            default:
                throw new \Exception('Access check not implemented for this type.');
        }
    }
}
