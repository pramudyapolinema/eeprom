@extends('adminlte::page')
@section('title', 'Buat Kegiatan')
@section('content_header')
    <h1 class="m-0 text-dark">Buat Kegiatan</h1>
@endsection

@section('content')
    <div class="card">
        <form enctype="multipart/form-data" id="addActivity">
            @csrf
            <div class="card-header">
                <h1 class="fs-3"><strong id="theTitle">Judul anda akan muncul disini...</strong></h1>
            </div>
            <div class="card-body">
                <x-adminlte-input name="title" label="Judul" placeholder="Masukkan Judul" disable-feedback />
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" cols="30" rows="30"></textarea>
            </div>
            <div class="card-footer d-flex">
                <div class="ml-auto">
                    <x-adminlte-button class="btn-flat" type="submit" label="Simpan" theme="success" id="submitButton" icon="fas fa-lg fa-save" />
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/tinymce/tinymce.bundle.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#description', // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: 'code table lists',
            toolbar: 'undo redo | formatselect| bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            skin: ($('body').hasClass('dark-mode') ? "oxide-dark" : ""),
            content_css: ($('body').hasClass('dark-mode') ? "dark" : "")
        });

        $('#title').on('input', function() {
            $('#theTitle').text($(this).val());
        })

        $('#addActivity').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $.ajax({
                url: "{{ route('activities.store') }}",
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                cache: false,
                data: new FormData(this),
                error: function(data) {
                    toastr.error(data.responseJSON.message, 'Error');
                },
                success: function(data) {
                    toastr.success(data.message, 'Sukses');
                }
            });
            $('#submitButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
