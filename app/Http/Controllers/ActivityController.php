<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $activities = Activity::all();
            return DataTables::of($activities)
                ->editColumn('author_id', function ($item) {
                    return $item->author->name;
                })
                ->editColumn('description', function ($item) {
                    return Str::limit(strip_tags($item->description), 100, '...');
                })
                ->editColumn('created_at', function ($item) {
                    return Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM Y HH:mm');
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editUserModal">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make();
        }
        return view('admin.activity.index');
    }

    public function create()
    {
        return view('admin.activity.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'title'         => 'required',
                'description'   => 'required',
            ], [
                'title.required'        => 'Masukkan judul!',
                'description.required'  => 'Masukkan deskripsi!'
            ]);

            $activity = Activity::create([
                'author_id'     => auth()->user()->id,
                'title'         => $request->title,
                'description'   => $request->description
            ]);

            foreach ($request->input('document', []) as $file) {
                $activity->medias()->create([
                    'media' => $file
                ]);
            }

            DB::commit();
            return response()->json([
                'message'   => 'Kegiatan berhasil disimpan!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message'   => $th->getMessage(),
            ], 500);
        }
    }

    public function show(Activity $activity)
    {
        //
    }

    public function edit(Activity $activity)
    {
        //
    }

    public function update(Request $request, Activity $activity)
    {
        //
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Activity::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
