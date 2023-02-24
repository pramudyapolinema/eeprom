<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DropdownController extends Controller
{
    public function getRoles()
    {
        $roles = Role::select('id', 'name')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'id' => $role->id,
                'text' => $role->name,
            ];
        }

        return response()->json(['results' => $data]);
    }
}
