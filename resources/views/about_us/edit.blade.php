@extends('layouts.sideBar')

@section('title', 'About Us')

@section('content')
<div class="container">
    <form action="{{ route('about_us.update') }}" method="POST">
        @csrf
        @method('put')

        <!-- Pre-fill the textarea with old value or model value -->
        <textarea id="value" name="value">{{ old('value', $aboutUs->value ?? '') }}</textarea>

        <button type="submit" class="btn btn-primary mt-3">Save</button>
    </form>

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
