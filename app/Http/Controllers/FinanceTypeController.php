<?php

namespace App\Http\Controllers;

use App\Models\FinanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FinanceTypeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $financeType = FinanceType::all();
            return DataTables::of($financeType)
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editTypesModal">
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
        return view('admin.finances.types.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            FinanceType::create([
                'name'  => $request->name,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil disimpan!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(FinanceType $financeType)
    {
        //
    }

    public function edit($id)
    {
        try {
            $financeType = FinanceType::findOrFail($id);
            return json_encode($financeType);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            FinanceType::findOrFail($id)->update([
                'name'  => $request->name,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            FinanceType::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
