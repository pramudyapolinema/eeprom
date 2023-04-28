<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Funding;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FundingController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $fundings = Funding::all();
            return DataTables::of($fundings)
                ->addColumn('name', function ($item) {
                    return $item->user->name;
                })
                ->addColumn('bank', function ($item) {
                    return $item->bank_account_id ? $item->bank_account->bank->nama_bank : 'Tunai';
                })
                ->editColumn('amount', function ($item) {
                    return 'Rp ' . number_format($item->amount + $item->unique_amount, 0, ',', '.');
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editFundingModal">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                })
                ->editColumn('status', function ($item) {
                    return '';
                })
                ->editColumn('status_updated_at', function ($item) {
                    return Carbon::parse($item->status_updated_at)->locale('id')->isoFormat('D MMMM Y H:m:s');
                })
                ->rawColumns(['status', 'actions'])
                ->addIndexColumn()
                ->make();
        }

        return view('admin.finances.funding.index');
    }


    public function create()
    {
        try {
            if (request()->data == 'user') {
                $data = User::role(['Alumni', 'SC', 'OC', 'BPH'])->select('id', 'name as text')->where([
                    ['name', 'like', '%' . request()->input('search', '') . '%']
                ])->get();
            } else if (request()->data == 'account') {
                $accounts = BankAccount::with('bank')
                    ->where('name', 'like', '%' . request()->input('search', '') . '%')
                    ->orWhereRelation('bank', 'nama_bank', 'like', '%' . request()->input('search', '') . '%')
                    ->get();
                $data = [];
                foreach ($accounts as $account) {
                    $data[] = [
                        'id' => $account->id,
                        'text' => $account->name . ' - ' . $account->bank->nama_bank,
                    ];
                }
            }
            return response()->json(['results' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            Funding::create([
                'user_id'   => $request->user_id,
                'bank_account_id' => $request->payment == 'tunai' ? null : $request->bank_account_id,
                'amount'    => $request->amount,
                'unique_amount' => rand(1, 99),
                'status'    => 1,
                'status_updated_at' => Carbon::now(),
                'note'  => $request->note,
                'payment_slip' => null,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil disimpan!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(Funding $funding)
    {
        //
    }

    public function edit(Funding $funding)
    {
        //
    }

    public function update(Request $request, Funding $funding)
    {
        //
    }

    public function destroy(Funding $funding)
    {
        //
    }
}
