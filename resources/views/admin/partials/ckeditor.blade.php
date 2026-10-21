{{--
    Turns textareas into CKEditor 5 rich text editors.
    Usage: @include('admin.partials.ckeditor', ['selectors' => ['#description'], 'rtlSelectors' => ['#description_ar']])
--}}
@php
    $selectors = $selectors ?? [];
    $rtlSelectors = $rtlSelectors ?? [];
@endphp

@once
    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ClassicEditor === 'undefined') {
                return;
            }

            const toolbar = [
                'heading', '|',
                'bold', 'italic', '|',
                'link', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ];

            function initEditor(selector, contentLanguage) {
                const el = document.querySelector(selector);
                if (!el) {
                    return;
                }
                ClassicEditor
                    .create(el, {
                        toolbar: toolbar,
                        language: { ui: 'en', content: contentLanguage }
                    })
                    .catch(error => console.error('CKEditor error:', error));
            }

            @json($selectors).forEach(selector => initEditor(selector, 'en'));
            @json($rtlSelectors).forEach(selector => initEditor(selector, 'ar'));
        });
    </script>
@endpush
