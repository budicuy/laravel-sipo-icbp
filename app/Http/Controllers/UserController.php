<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%'.$search.'%')
                    ->orWhere('nama_lengkap', 'like', '%'.$search.'%')
                    ->orWhere('role', 'like', '%'.$search.'%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status == 'Aktif' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Sorting
        $sortField = $request->get('sort', 'id_user');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['username', 'nama_lengkap', 'role', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id_user', 'desc');
        }

        $perPage = $request->get('per_page', 50);
        $users = $query->paginate($perPage);

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:user,username',
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'nullable|string|max:20',
            'role' => 'required|string|in:Super Admin,Admin,User',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nik.max' => 'NIK maksimal 20 karakter',
            'role.required' => 'Role wajib dipilih',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        User::create([
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => 1,
        ]);

        return redirect()->route('user.index')->with('success', 'Data user berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('user', 'username')->ignore($user->id_user, 'id_user')],
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'nullable|string|max:20',
            'role' => 'required|string|in:Super Admin,Admin,User',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nik.max' => 'NIK maksimal 20 karakter',
            'role.required' => 'Role wajib dipilih',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $updateData = [
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'],
            'role' => $validated['role'],
        ];

        // Update password jika diisi
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Update status aktif jika ada
        if ($request->has('is_active')) {
            $updateData['is_active'] = $request->is_active ? 1 : 0;
        }

        $user->update($updateData);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Cek jika user yang akan dihapus adalah user yang sedang login
        if (Auth::check() && Auth::id() == $user->id_user) {
            return redirect()->route('user.index')->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Data user berhasil dihapus');
    }
}
