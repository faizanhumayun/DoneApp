<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {}

    /**
     * Download an attachment.
     */
    public function download(Attachment $attachment)
    {
        try {
            return $this->attachmentService->downloadAttachment($attachment, Auth::user());
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
    }

    /**
     * Delete an attachment.
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        try {
            $this->attachmentService->deleteAttachment($attachment, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
