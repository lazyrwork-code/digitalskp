<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::when($search, fn($q) =>
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
            )
            ->whereIn('role', ['pegawai', 'admin'])
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('kepala.kelola-user', compact('users', 'search'));
    }

    public function create()
    {
        return view('kepala.user-form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'nip'      => 'nullable|string|max:30',
            'role'     => 'required|in:pegawai,admin',
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'email'    => $request->email,
            'nip'      => preg_replace('/\s+/', '', $request->nip),
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('kepala.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('kepala.user-form', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email'    => 'required|email|unique:users,email,' . $id,
            'nip'      => 'nullable|string|max:30',
            'role'     => 'required|in:pegawai,admin',
            'password' => ['nullable', Password::defaults(), 'confirmed'],
        ]);

        $data = [
            'nama'     => $request->nama,
            'username' => $request->username,
            'email'    => $request->email,
            'nip'      => preg_replace('/\s+/', '', $request->nip),
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('kepala.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('kepala.user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}