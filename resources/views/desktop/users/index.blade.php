@extends('layouts.desktop')

@section('title', 'Kelola Staf & User - E-Kasir')
@section('page_title', 'Manajemen Pengguna & Staf')

@section('content')
<div x-data="{ showAddModal: false, editModal: false, selectedUser: {} }">
    <!-- Action Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Daftar Pengguna & Kasir</h3>
            <p class="text-xs text-slate-400">Pengaturan akses role & PIN kasir</p>
        </div>

        <button @click="showAddModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/30 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Tambah Pengguna</span>
        </button>
    </div>

    <!-- Users Datatable -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-4">Nama Pengguna</th>
                        <th class="py-4 px-4">Email</th>
                        <th class="py-4 px-4">Role / Hak Akses</th>
                        <th class="py-4 px-4">Outlet Penugasan</th>
                        <th class="py-4 px-4">PIN Kasir</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $u->name }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $u->email }}</td>
                            <td class="py-3.5 px-4">
                                @if($u->role === 'superadmin')
                                    <span class="px-2.5 py-1 bg-purple-100 text-purple-700 font-black rounded-lg uppercase text-[10px]">Superadmin</span>
                                @elseif($u->role === 'manager')
                                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 font-black rounded-lg uppercase text-[10px]">Manager</span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-black rounded-lg uppercase text-[10px]">Kasir</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $u->tenant->name ?? 'Global / Semua' }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">{{ $u->pin ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="editModal = true; selectedUser = {{ json_encode($u) }}" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('desktop.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Add User -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base">Tambah Pengguna Baru</h3>

            <form action="{{ route('desktop.users.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Nama staf..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="staf@ekasir.com" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role / Peran</label>
                    <select name="role" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        <option value="cashier">Kasir</option>
                        <option value="manager">Manager Outlet</option>
                        <option value="superadmin">Superadmin (Owner)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Outlet</label>
                    <select name="tenant_id" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        <option value="">-- Bebas / Superadmin --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">PIN Kasir (6 Digit)</label>
                        <input type="text" name="pin" maxlength="6" value="123456" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono text-center">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base">Edit Pengguna</h3>

            <form :action="'/desktop/users/' + selectedUser.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="selectedUser.name" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" x-model="selectedUser.email" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role / Peran</label>
                    <select name="role" x-model="selectedUser.role" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        <option value="cashier">Kasir</option>
                        <option value="manager">Manager Outlet</option>
                        <option value="superadmin">Superadmin (Owner)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Outlet</label>
                    <select name="tenant_id" x-model="selectedUser.tenant_id" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        <option value="">-- Bebas / Superadmin --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Ubah Password (Kosongkan jika tetap)</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">PIN Kasir (6 Digit)</label>
                        <input type="text" name="pin" x-model="selectedUser.pin" maxlength="6" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono text-center">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
