@extends('adminlte::page')
@section('title', 'Banks')
@section('content_header')
    <h1 class="m-0 text-dark">Banks</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Bank', 'Nama', 'Nomor Rekening', 'Tanggal Dibuat', ['label' => 'Actions', 'width' => 5]];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('finances.banks.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'bank'], ['data' => 'name'], ['data' => 'account_number'], ['data' => 'created_at'], ['data' => 'actions']],
    ];
@endphp
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                <x-adminlte-button label="Bank" theme="primary" icon="fas fa-plus" data-toggle="modal" data-target="#addBankModal" />
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-banks" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    <form id="addBank">
        <x-adminlte-modal id="addBankModal" title="Add Bank">
            @csrf
            <div class="form-group">
                <label for="bank_id" class="form-label">Bank</label>
                <select name="bank_id" id="bank_id" class="form-control">
                </select>
            </div>
            <x-adminlte-input name="name" label="Nama Rekening Bank" placeholder="Masukkan nama rekening bank" disable-feedback />
            <x-adminlte-input name="account_number" label="Nomor Rekening" placeholder="Masukkan nomor rekening" disable-feedback />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
    <form id="editBank">
        <x-adminlte-modal id="editBankModal" title="Edit Bank">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="form-group">
                <label for="editBank_id" class="form-label">Bank</label>
                <select name="bank_id" id="editBank_id" class="form-control">
                </select>
            </div>
            <x-adminlte-input name="name" id="editName" label="Nama Rekening Bank" placeholder="Masukkan nama rekening bank" disable-feedback />
            <x-adminlte-input name="account_number" id="editAccount_number" label="Nomor Rekening" placeholder="Masukkan nomor rekening" disable-feedback />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitEditButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissEditButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
@endsection

@section('js')
    <script>
        $('#bank_id').select2({
            ajax: {
                url: "{{ route('finances.banks.create') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Bank",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#addBank'),
        });

        $('#editBank_id').select2({
            ajax: {
                url: "{{ route('finances.banks.create') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                        edit: 1,
                    }
                },
            },
            placeholder: "Pilih Bank",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#editBank'),
        });

        $('#addBank').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $.ajax({
                url: "{{ route('finances.banks.store') }}",
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
                    $('#addBankModal').modal('toggle');
                    $('#table-banks').DataTable().ajax.reload();
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
                    var route = "{{ route('finances.banks.destroy', ':id') }}";
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
                            $('#table-banks').DataTable().ajax.reload();
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
            var route = "{{ route('finances.banks.edit', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editAccount_number').val(data.account_number);
                    $('#editBank_id').append($("<option selected></option>").val(data.bank_id).text(data.bank.nama_bank)).trigger('change');
                }
            });
        });

        $('#editBank').on('submit', function(e) {
            e.preventDefault();
            $('#submitEditButton').attr('disabled', true);
            var id = $('#editId').val();
            var route = "{{ route('finances.banks.update', ':id') }}";
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
                    $('#editBankModal').modal('toggle');
                    $('#table-banks').DataTable().ajax.reload();
                }
            });
            $('#submitEditButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
