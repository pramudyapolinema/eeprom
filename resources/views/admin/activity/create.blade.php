@extends('adminlte::page')
@section('title', 'Buat Kegiatan')
@section('content_header')
    <h1 class="m-0 text-dark">Buat Kegiatan</h1>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">

            </div>
            <div class="card-body">
                <x-adminlte-input name="title" label="Judul" placeholder="Masukkan Judul" disable-feedback />
            </div>
            <div class="card-footer d-flex">
                <div class="ml-auto">
                    <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $('#role').select2({
            ajax: {
                url: "{{ route('dropdown.roles') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Role",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#addActivity'),
        });

        $('#editRole').select2({
            ajax: {
                url: "{{ route('dropdown.roles') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Role",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#editActivity'),
        });

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
                    $('#addActivityModal').modal('toggle');
                    $('#table-activities').DataTable().ajax.reload();
                }
            });
            $('#submitButton').removeAttr('disabled');
            return false;
        });

        $(document).on("click", "#deleteButton", function(e) {
            e.preventDefault();
            Swal.fire({
                customClass: {
                    confirmButton: 'bg-danger',
                },
                title: 'Apakah anda yakin?',
                text: "Apakah anda yakin ingin menghapus data ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    var route = "{{ route('activities.destroy', ':id') }}";
                    route = route.replace(':id', id);
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        data: {
                            _token: $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 1000,
                                timerProgressBar: true,
                            })
                            $('#table-activities').DataTable().ajax.reload();
                        },
                        error: function(data) {
                            toastr.error(data.responseJSON.message, 'Error');
                        }
                    });
                }
            });
        });

        $(document).on("click", "#editButton", function(e) {
            var id = $(this).data("id");
            var route = "{{ route('activities.edit', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editActivityname').val(data.activityname);
                    $('#editPhonenumber').val(data.phonenumber);
                    $('#editEmail').val(data.email);
                    $('#editRole').val(data.roles[0].id).trigger('change');
                }
            });
        });

        $('#editActivity').on('submit', function(e) {
            e.preventDefault();
            $('#submitEditButton').attr('disabled', true);
            var id = $('#editId').val();
            var route = "{{ route('activities.update', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                    $('#editActivityModal').modal('toggle');
                    $('#table-activities').DataTable().ajax.reload();
                }
            });
            $('#submitEditButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
