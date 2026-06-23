<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles')->orderBy('created_at', 'desc')->get();
        return response()->json($admins);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:admins,username',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6',
            'is_active' => 'nullable|boolean',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $admin = Admin::create([
            'username' => $data['username'],
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (!empty($data['role_ids'])) {
            $admin->roles()->sync($data['role_ids']);
        }

        return response()->json($admin->load('roles'), 201);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $data = $request->validate([
            'username' => 'sometimes|string|max:255|unique:admins,username,' . $id,
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $updateData = collect($data)->except(['password', 'role_ids'])->toArray();
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        $admin->update($updateData);

        if (array_key_exists('role_ids', $data)) {
            $admin->roles()->sync($data['role_ids'] ?? []);
        }

        return response()->json($admin->fresh()->load('roles'));
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->username === 'admin') {
            return response()->json(['error' => 'Tidak dapat menghapus admin utama'], 403);
        }
        $admin->delete();
        return response()->json(['message' => 'Admin dihapus']);
    }
}
