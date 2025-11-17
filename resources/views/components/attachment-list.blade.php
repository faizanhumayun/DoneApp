@props([
    'attachments' => [],
    'canDelete' => false,
])

@if($attachments->count() > 0)
<div class="attachments-list" x-data="attachmentList()">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] uppercase tracking-wide">
            Attachments ({{ $attachments->count() }})
        </h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($attachments as $attachment)
            <div class="attachment-card bg-white dark:bg-[#161615] border border-[#E8E8E8] dark:border-[#3E3E3A] rounded-lg p-4 hover:border-[#D6D6D6] hover:shadow-sm transition-all" data-attachment-id="{{ $attachment->id }}">
                <div class="flex items-start gap-3">
                    <!-- File Icon -->
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-[#F9F9F9] dark:bg-[#0a0a0a] rounded-md">
                        @if($attachment->isImage())
                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @elseif($attachment->isDocument())
                            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @else
                            <svg class="w-7 h-7 text-[#706f6c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1 truncate" title="{{ $attachment->original_name }}">
                            {{ $attachment->original_name }}
                        </p>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">
                            {{ $attachment->formatted_size }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 items-center">
                            <!-- Download Button -->
                            <a href="{{ route('attachments.download', $attachment) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>

                            @if($canDelete)
                                <!-- Delete Button -->
                                <button type="button"
                                        @click="deleteAttachment({{ $attachment->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Storage Badge -->
                <div class="mt-3 pt-3 border-t border-[#F0F0F0] dark:border-[#3E3E3A]">
                    <span class="inline-flex items-center gap-1.5 text-[10px] text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wide">
                        @if($attachment->storage_disk === 'spaces')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            Cloud Storage
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                            </svg>
                            Local Storage
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function attachmentList() {
        return {
            deleteAttachment(attachmentId) {
                if (!confirm('Are you sure you want to delete this attachment? This action cannot be undone.')) {
                    return;
                }

                const card = document.querySelector(`[data-attachment-id="${attachmentId}"]`);

                // Show loading state
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.pointerEvents = 'none';
                }

                fetch(`/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Animate and remove the card
                        if (card) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                // Reload if no attachments left
                                if (document.querySelectorAll('.attachment-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    } else {
                        alert(data.message || 'Failed to delete attachment');
                        // Restore card state
                        if (card) {
                            card.style.opacity = '1';
                            card.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the attachment');
                    // Restore card state
                    if (card) {
                        card.style.opacity = '1';
                        card.style.pointerEvents = 'auto';
                    }
                });
            }
        }
    }
</script>
@else
    <!-- No attachments message -->
    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] italic py-4">
        No attachments yet.
    </div>
@endif
