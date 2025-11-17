@props([
    'name' => 'description',
    'label' => 'Description',
    'value' => '',
    'placeholder' => 'Enter description...',
    'required' => false,
    'height' => '150px',
    'helpText' => null,
    'teamMembers' => null,
])

@php
    $editorId = 'quill_editor_' . str_replace(['[', ']', '.'], '_', $name);
    $hiddenInputId = 'hidden_' . str_replace(['[', ']', '.'], '_', $name);
@endphp

<div>
    @if($label)
        <label for="{{ $editorId }}" class="block text-sm font-medium mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Quill Editor Container -->
    <div
        id="{{ $editorId }}"
        class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm @error($name) border-red-500 @enderror"
        style="min-height: {{ $height }};"
    ></div>

    <!-- Hidden input to store the content for form submission -->
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $hiddenInputId }}"
        value="{{ old($name, $value) }}"
    >

    @if($helpText)
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $helpText }}</p>
    @endif

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

@once
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        @if($teamMembers !== null)
            <link href="https://cdn.jsdelivr.net/npm/quill-mention@3.2.0/dist/quill.mention.min.css" rel="stylesheet">
            <style>
                /* Limit mention list height and add scrolling */
                .ql-mention-list-container {
                    max-height: 200px !important;
                    overflow-y: auto !important;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                }
                .mention-item {
                    padding: 8px 12px;
                    cursor: pointer;
                }
                .mention-item:hover {
                    background-color: #f5f5f5;
                }
                .dark .mention-item:hover {
                    background-color: #3E3E3A;
                }
            </style>
        @endif
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        @if($teamMembers !== null)
            <script src="https://cdn.jsdelivr.net/npm/quill-mention@3.2.0/dist/quill.mention.min.js"></script>
        @endif
    @endpush
@endonce

@push('scripts')
<script>
    (function() {
        @if($teamMembers !== null)
        // Team members data for mentions
        var teamMembers_{{ $editorId }} = {!! json_encode($teamMembers) !!};
        @endif

        // Initialize Quill editor for {{ $editorId }}
        var modules_{{ $editorId }} = {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        };

        @if($teamMembers !== null)
        // Add mention module if team members are provided
        modules_{{ $editorId }}.mention = {
            allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
            mentionDenotationChars: ["@"],
            source: function(searchTerm, renderList, mentionChar) {
                let values;
                if (searchTerm.length === 0) {
                    values = teamMembers_{{ $editorId }};
                } else {
                    const matches = [];
                    for (let i = 0; i < teamMembers_{{ $editorId }}.length; i++) {
                        if (~teamMembers_{{ $editorId }}[i].value.toLowerCase().indexOf(searchTerm.toLowerCase())) {
                            matches.push(teamMembers_{{ $editorId }}[i]);
                        }
                    }
                    values = matches;
                }
                renderList(values, searchTerm);
            },
            renderItem: function(item, searchTerm) {
                return '<div class="mention-item"><strong>' + item.value + '</strong></div>';
            }
        };
        @endif

        var quill_{{ $editorId }} = new Quill('#{{ $editorId }}', {
            theme: 'snow',
            placeholder: '{{ $placeholder }}',
            modules: modules_{{ $editorId }}
        });

        // Load existing content
        var existingContent_{{ $editorId }} = {!! json_encode(old($name, $value)) !!};
        if (existingContent_{{ $editorId }}) {
            // Check if content is HTML or plain text
            if (existingContent_{{ $editorId }}.indexOf('<') !== -1) {
                quill_{{ $editorId }}.root.innerHTML = existingContent_{{ $editorId }};
            } else {
                quill_{{ $editorId }}.setText(existingContent_{{ $editorId }});
            }
        }

        // Update hidden input on text change
        quill_{{ $editorId }}.on('text-change', function() {
            var html = quill_{{ $editorId }}.root.innerHTML;
            // If editor is empty, set to null
            if (quill_{{ $editorId }}.getText().trim().length === 0) {
                document.getElementById('{{ $hiddenInputId }}').value = '';
            } else {
                document.getElementById('{{ $hiddenInputId }}').value = html;
            }
        });

        // Before form submit, update hidden field
        var form_{{ $editorId }} = document.getElementById('{{ $editorId }}').closest('form');
        if (form_{{ $editorId }}) {
            form_{{ $editorId }}.addEventListener('submit', function() {
                var html = quill_{{ $editorId }}.root.innerHTML;
                if (quill_{{ $editorId }}.getText().trim().length === 0) {
                    document.getElementById('{{ $hiddenInputId }}').value = '';
                } else {
                    document.getElementById('{{ $hiddenInputId }}').value = html;
                }
            });
        }
    })();
</script>
@endpush
