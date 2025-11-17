@props([
    'name' => 'attachments',
    'storageDisk' => 'local',
    'multiple' => true,
    'existingFiles' => [],
])

<div x-data="fileUpload()" x-init="init()" class="file-upload-component">
    <!-- Storage Selector -->
    <div class="storage-selector-wrapper" style="margin-bottom: 16px;">
        <label class="storage-label" style="font-size: 13px; font-weight: 600; color: #3A3A3A; margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: 0.5px;">
            Storage Location
        </label>
        <div class="storage-options" style="display: flex; gap: 12px;">
            <label class="storage-option" style="flex: 1; cursor: pointer;">
                <input type="radio" name="storage_disk" value="local" x-model="storageDisk" style="margin-right: 8px;" {{ $storageDisk === 'local' ? 'checked' : '' }}>
                <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                </svg>
                <span style="font-size: 14px; color: #1b1b18;">Local Storage</span>
            </label>
            <label class="storage-option" style="flex: 1; cursor: pointer;">
                <input type="radio" name="storage_disk" value="spaces" x-model="storageDisk" style="margin-right: 8px;" {{ $storageDisk === 'spaces' ? 'checked' : '' }}>
                <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
                <span style="font-size: 14px; color: #1b1b18;">Cloud Storage</span>
            </label>
        </div>
    </div>

    <!-- Drag & Drop Area -->
    <div
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop($event)"
        :class="{ 'drag-over': dragOver }"
        class="drop-zone"
        style="border: 2px dashed #D6D6D6; border-radius: 8px; padding: 32px; text-align: center; background: #FAFAFA; transition: all 0.2s; cursor: pointer;"
        @click="$refs.fileInput.click()">

        <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: #9B9B9B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>

        <p style="font-size: 15px; color: #1b1b18; font-weight: 500; margin-bottom: 8px;">
            Drop files here or click to browse
        </p>
        <p style="font-size: 13px; color: #706f6c;">
            Maximum file size: 50MB
        </p>
        <p style="font-size: 13px; color: #706f6c;">
            Allowed: Images, Documents, PDFs, Archives
        </p>
    </div>

    <!-- File Input -->
    <input
        type="file"
        ref="fileInput"
        @change="handleFileSelect($event)"
        {{ $multiple ? 'multiple' : '' }}
        style="display: none;"
        accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z,.json,.xml"
    >

    <!-- File List -->
    <div x-show="files.length > 0" style="margin-top: 20px;">
        <p style="font-size: 13px; font-weight: 600; color: #3A3A3A; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            Selected Files (<span x-text="files.length"></span>)
        </p>

        <div class="file-list" style="display: flex; flex-direction: column; gap: 8px;">
            <template x-for="(file, index) in files" :key="index">
                <div class="file-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border: 1px solid #E8E8E8; border-radius: 6px;">
                    <!-- File Icon -->
                    <div class="file-icon" style="flex-shrink: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #F9F9F9; border-radius: 4px;">
                        <svg style="width: 24px; height: 24px; color: #706f6c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <!-- File Info -->
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 14px; font-weight: 500; color: #1b1b18; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="file.name"></p>
                        <p style="font-size: 12px; color: #706f6c;" x-text="formatFileSize(file.size)"></p>
                    </div>

                    <!-- Remove Button -->
                    <button type="button" @click="removeFile(index)" style="flex-shrink: 0; padding: 8px; color: #EF4444; hover:background: #FEE2E2; border: none; border-radius: 4px; cursor: pointer; transition: all 0.2s;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Hidden inputs for form submission -->
    <template x-for="(file, index) in files" :key="index">
        <input type="hidden" :name="'{{ $name }}[' + index + ']'" :value="file.dataUrl">
    </template>
</div>

<style>
    .drop-zone.drag-over {
        border-color: #7BB3FF !important;
        background: #F0F7FF !important;
    }

    .file-item:hover {
        border-color: #D6D6D6;
    }

    .storage-option {
        padding: 12px;
        border: 1px solid #E8E8E8;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .storage-option:has(input:checked) {
        border-color: #7BB3FF;
        background: #F0F7FF;
    }

    .storage-option:hover {
        border-color: #D6D6D6;
    }
</style>

<script>
    function fileUpload() {
        return {
            files: [],
            dragOver: false,
            storageDisk: '{{ $storageDisk }}',

            init() {
                // Initialize with existing files if any
            },

            handleFileSelect(event) {
                this.addFiles(event.target.files);
                event.target.value = ''; // Reset input
            },

            handleDrop(event) {
                this.dragOver = false;
                this.addFiles(event.dataTransfer.files);
            },

            async addFiles(fileList) {
                for (let file of fileList) {
                    // Validate file size (50MB)
                    if (file.size > 52428800) {
                        alert(`File "${file.name}" exceeds the maximum size of 50MB`);
                        continue;
                    }

                    // Convert file to base64 for form submission
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.files.push({
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            dataUrl: e.target.result,
                        });
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeFile(index) {
                this.files.splice(index, 1);
            },

            formatFileSize(bytes) {
                if (bytes >= 1073741824) {
                    return (bytes / 1073741824).toFixed(2) + ' GB';
                } else if (bytes >= 1048576) {
                    return (bytes / 1048576).toFixed(2) + ' MB';
                } else if (bytes >= 1024) {
                    return (bytes / 1024).toFixed(2) + ' KB';
                } else {
                    return bytes + ' bytes';
                }
            }
        }
    }
</script>
