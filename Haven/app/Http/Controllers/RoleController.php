<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Danh sách Role
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    // Hiển thị form tạo Role
    public function create()
    {
        return view('roles.create');
    }

    // Lưu Role mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }


    // Hiển thị thông tin Role
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    // Hiển thị form chỉnh sửa Role
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    // Cập nhật Role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    // Xóa Role
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}

