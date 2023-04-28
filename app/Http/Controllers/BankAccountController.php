<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $bankAccount = BankAccount::all();
            return DataTables::of($bankAccount)
                ->addColumn('bank', function ($item) {
                    return $item->bank->nama_bank;
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editBankModal">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.finances.banks.index');
    }

    public function create()
    {
        $banks = Bank::select('id', 'nama_bank')
            ->where([
                ['nama_bank', 'like', '%' . request()->input('search', '') . '%']
            ])
            ->when(!request()->edit, function ($query) {
                return $query->doesntHave('account');
            })
            ->get();
        $data = [];
        foreach ($banks as $bank) {
            $data[] = [
                'id' => $bank->id,
                'text' => $bank->nama_bank,
            ];
        }

        return response()->json(['results' => $data]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'bank_id'   => 'required|unique:bank_accounts,bank_id',
                'name'      => 'required',
                'account_number'    => 'required|unique:bank_accounts,account_number'
            ], [
                'bank_id.required'  => 'Masukkan Id Bank!',
                'bank_id.unique'    => 'Rekening dengan bank tersebut telah ditambahkan!',
                'name.required'     => 'Masukkan nama pemilik rekening!',
                'account_number.required'   => 'Masukkan nomor rekening!',
                'account_number.unique'     => 'Nomor rekening tersebut telah ditambahkan!',
            ]);

            BankAccount::create([
                'bank_id'   => $request->bank_id,
                'name'      => $request->name,
                'account_number'    => $request->account_number,
            ]);

            DB::commit();
            return response()->json(['message' => 'Rekening berhasil ditambahkan!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(BankAccount $bankAccount)
    {
        //
    }

    public function edit($id)
    {
        try {
            $bankAccount = BankAccount::with('bank')->findOrFail($id);
            return response()->json($bankAccount, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 200);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'bank_id'   => 'required|unique:bank_accounts,bank_id,' . $id,
                'name'      => 'required',
                'account_number'    => 'required|unique:bank_accounts,account_number,' . $id
            ], [
                'bank_id.required'  => 'Masukkan Id Bank!',
                'bank_id.unique'    => 'Rekening dengan bank tersebut telah ditambahkan!',
                'name.required'     => 'Masukkan nama pemilik rekening!',
                'account_number.required'   => 'Masukkan nomor rekening!',
                'account_number.unique'     => 'Nomor rekening tersebut telah ditambahkan!',
            ]);

            BankAccount::findOrFail($id)->update([
                'bank_id'   => $request->bank_id,
                'name'      => $request->name,
                'account_number'    => $request->account_number,
            ]);

            DB::commit();
            return response()->json(['message' => 'Rekening berhasil diupdate!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            BankAccount::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Rekening berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
