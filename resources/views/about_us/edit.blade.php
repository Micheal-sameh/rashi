@extends('layouts.sideBar')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <x-page-header icon="fa-edit" :title="__('messages.edit') . ' ' . __('messages.about_us')">
        <x-slot:actions>
            <a href="{{ route("$route.show") }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </x-slot>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form action="{{ route("$route.update") }}" method="POST">
                @csrf
                @method('put')

                <label class="form-label rs-label-md">{{ __('messages.about_us') }}</label>
                <!-- Pre-fill the textarea with old value or model value -->
                <textarea id="value" name="value">{{ old('value', $aboutUs->value ?? '') }}</textarea>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/{{ env('TINY_API_KEY') }}/tinymce/6/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: 'textarea',
        plugins: [
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists',
            'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed',
            'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste',
            'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions',
            'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
            'autocorrect', 'typography', 'inlinecss', 'markdown','importword',
            'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
        uploadcare_public_key: 'c3b5d64c330bdebe95f4',
    });
    </script>
</div>
@endsection
