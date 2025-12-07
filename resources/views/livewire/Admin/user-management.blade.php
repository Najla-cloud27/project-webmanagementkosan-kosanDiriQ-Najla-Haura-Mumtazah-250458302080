@php
    $header = 'Kelola Pengguna';
@endphp

<div>
    <!-- Success/Error Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <button wire:click="openModal" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengguna</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari pengguna...">
                            <div class="input-group-append">
                                <button class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Avatar</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <img src="{{ \App\Helpers\AvatarHelper::getAvatar($user, 50) }}" 
                                         alt="{{ $user->name }}" 
                                         class="img-circle elevation-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'pemilik')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-user-shield"></i> Pemilik
                                        </span>
                                    @else
                                        <span class="badge badge-info">
                                            <i class="fas fa-user"></i> Penyewa
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(Auth::id() != $user->id)
                                    <button wire:click="delete({{ $user->id }})" 
                                            onclick="return confirm('Yakin ingin menghapus pengguna ini?')"
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i> Tidak ada data pengguna
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $editMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h4>
                    <button type="button" wire:click="closeModal" class="close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form>
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" wire:model="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="Contoh: 08123456789">
                            @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>NIK</label>
                            <input type="text" wire:model="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="Masukkan NIK (16 digit)">
                            @error('nik') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Role <span class="text-danger">*</span></label>
                            <select wire:model="role" class="form-control @error('role') is-invalid @enderror">
                                <option value="penyewa">Penyewa</option>
                                <option value="pemilik">Pemilik/Admin</option>
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Password @if(!$editMode)<span class="text-danger">*</span>@else<small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>@endif</label>
                            <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password @if(!$editMode)<span class="text-danger">*</span>@endif</label>
                            <input type="password" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password">
                            @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" wire:click="closeModal" class="btn btn-default">Batal</button>
                    <button type="button" wire:click="save" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $editMode ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
