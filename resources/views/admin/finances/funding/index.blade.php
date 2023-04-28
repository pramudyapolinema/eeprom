@extends('adminlte::page')
@section('title', 'Fundings')
@section('content_header')
    <h1 class="m-0 text-dark">Fundings</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Nama', 'Bank', 'Jumlah', 'Status', 'Status diupdate pada', ['label' => 'Actions', 'width' => 5]];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('finances.fundings.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'name'], ['data' => 'bank'], ['data' => 'amount'], ['data' => 'status'], ['data' => 'status_updated_at'], ['data' => 'actions']],
    ];
@endphp
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                <x-adminlte-button label="Funding" theme="primary" icon="fas fa-plus" data-toggle="modal" data-target="#addFundingModal" />
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-fundings" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    <form id="addFunding">
        <x-adminlte-modal id="addFundingModal" title="Add Funding">
            @csrf
            <div class="form-group">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" id="user_id" class="form-control">
                </select>
            </div>
            <div class="form-group">
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" id="tunai" name="payment" value="tunai">
                    <label for="tunai" class="custom-control-label">Tunai</label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" id="nontunai" name="payment" value="nontunai">
                    <label for="nontunai" class="custom-control-label">Non Tunai</label>
                </div>
            </div>
            <div class="form-group" id="bank_account_id_wrapper" style="display: none;">
                <label for="bank_account_id" class="form-label">Rekening</label>
                <select name="bank_account_id" id="bank_account_id" class="form-control">
                </select>
            </div>
            <x-adminlte-input name="amount" label="Jumlah" placeholder="Masukkan jumlah pendanaan" disable-feedback />
            <div class="form-group">
                <label for="note">Catatan</label>
                <textarea name="note" id="note" cols="30" rows="30"></textarea>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
    <form id="editFunding">
        <x-adminlte-modal id="editFundingModal" title="Edit Funding">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="form-group">
                <label for="editFunding_id" class="form-label">Funding</label>
                <select name="funding_id" id="editFunding_id" class="form-control">
                </select>
            </div>
            <x-adminlte-input name="name" id="editName" label="Nama Rekening Funding" placeholder="Masukkan nama rekening funding" disable-feedback />
            <x-adminlte-input name="account_number" id="editAccount_number" label="Nomor Rekening" placeholder="Masukkan nomor rekening" disable-feedback />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitEditButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissEditButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/tinymce/tinymce.bundle.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#note',
            plugins: 'code table lists',
            toolbar: 'undo redo | formatselect| bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            skin: ($('body').hasClass('dark-mode') ? "oxide-dark" : ""),
            content_css: ($('body').hasClass('dark-mode') ? "dark" : "")
        });

        $('#user_id').select2({
            ajax: {
                url: "{{ route('finances.fundings.create') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                        data: 'user',
                    }
                },
            },
            placeholder: "Pilih User",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#addFunding'),
        });

        $('#bank_account_id').select2({
            ajax: {
                url: "{{ route('finances.fundings.create') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                        data: 'account'
                    }
                },
            },
            placeholder: "Pilih Rekening",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#addFunding'),
        });

        $('#editFunding_id').select2({
            ajax: {
                url: "{{ route('finances.fundings.create') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                        edit: 1,
                    }
                },
            },
            placeholder: "Pilih Funding",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#editFunding'),
        });

        $('#addFunding').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $.ajax({
                url: "{{ route('finances.fundings.store') }}",
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
                    $('#addFundingModal').modal('toggle');
                    $('#table-fundings').DataTable().ajax.reload();
                }
            });
            $('#submitButton').removeAttr('disabled');
            return false;
        });

        $('input[type=radio][name=payment]').change(function() {
            if ($(this).val() == 'tunai') {
                $('#bank_account_id_wrapper').hide();
            } else {
                $('#bank_account_id_wrapper').show();
            }
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
                    var route = "{{ route('finances.fundings.destroy', ':id') }}";
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
                            $('#table-fundings').DataTable().ajax.reload();
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
            var route = "{{ route('finances.fundings.edit', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editAccount_number').val(data.account_number);
                    $('#editFunding_id').append($("<option selected></option>").val(data.funding_id).text(data.funding.nama_funding)).trigger('change');
                }
            });
        });

        $('#editFunding').on('submit', function(e) {
            e.preventDefault();
            $('#submitEditButton').attr('disabled', true);
            var id = $('#editId').val();
            var route = "{{ route('finances.fundings.update', ':id') }}";
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
                    $('#editFundingModal').modal('toggle');
                    $('#table-fundings').DataTable().ajax.reload();
                }
            });
            $('#submitEditButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
