@extends('adminlte::page')
@section('title', 'Kegiatan')
@section('content_header')
    <h1 class="m-0 text-dark">Kegiatan</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Nama', 'Pembuat', 'Deskripsi singkat', 'Tanggal Pembuatan', ['label' => 'Actions', 'width' => 5]];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('activities.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'title'], ['data' => 'author_id'], ['data' => 'description'], ['data' => 'created_at'], ['data' => 'actions']],
    ];
@endphp
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                <a class="btn btn-primary" href="{{ route('activities.create') }}"><i class="fas fa-plus"></i> Kegiatan</a>
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-activities" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
@endsection

@section('js')
    <script>
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
    </script>
@endsection
