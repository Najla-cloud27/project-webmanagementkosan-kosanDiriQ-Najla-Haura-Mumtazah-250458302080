@php
    $header = 'Profil Saya';
@endphp

<div>
    <!-- Success/Error Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('message') }}
        </div>
    @endif

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if(Auth::user()->avatar_url)
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="{{ asset('storage/avatars/' . Auth::user()->avatar_url) }}" 
                                 alt="User profile picture">
                        @else
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="{{ \App\Helpers\AvatarHelper::generate(Auth::user()->name, 200) }}" 
                                 alt="User profile picture">
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                    <p class="text-muted text-center">
                        @if(Auth::user()->role == 'pemilik')
                            <span class="badge badge-danger">
                                <i class="fas fa-user-shield"></i> Pemilik/Admin
                            </span>
                        @else
                            <span class="badge badge-info">
                                <i class="fas fa-user"></i> Penyewa
                            </span>
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b><i class="fas fa-envelope mr-2"></i>Email</b>
                            <a class="float-right text-sm">{{ Auth::user()->email }}</a>
                        </li>
                        @if(Auth::user()->phone_number)
                        <li class="list-group-item">
                            <b><i class="fas fa-phone mr-2"></i>Telepon</b>
                            <a class="float-right text-sm">{{ Auth::user()->phone_number }}</a>
                        </li>
                        @endif
                        @if(Auth::user()->nik)
                        <li class="list-group-item">
                            <b><i class="fas fa-id-card mr-2"></i>NIK</b>
                            <a class="float-right text-sm">{{ Auth::user()->nik }}</a>
                        </li>
                        @endif
                        <li class="list-group-item">
                            <b><i class="fas fa-calendar mr-2"></i>Bergabung</b>
                            <a class="float-right text-sm">{{ Auth::user()->created_at->format('d M Y') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile" data-toggle="tab">
                                <i class="fas fa-user"></i> Edit Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#password" data-toggle="tab">
                                <i class="fas fa-lock"></i> Ubah Password
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Profile Tab -->
                        <div class="active tab-pane" id="profile">
                            <form wire:submit.prevent="updateProfile">
                                <div class="form-group">
                                    <label>Foto Profil</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" wire:model="avatar" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatarFile" accept="image/*">
                                            <label class="custom-file-label" for="avatarFile">
                                                {{ $avatar ? $avatar->getClientOriginalName() : 'Pilih file...' }}
                                            </label>
                                        </div>
                                    </div>
                                    @error('avatar') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    @if($avatar)
                                        <div class="mt-2">
                                            <img src="{{ $avatar->temporaryUrl() }}" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif
                                    <small class="text-muted">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                </div>

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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Password Tab -->
                        <div class="tab-pane" id="password">
                            <form wire:submit.prevent="updatePassword">
                                <div class="alert alert-info">
                                    <i class="icon fas fa-info-circle"></i>
                                    Pastikan password baru Anda kuat dan mudah diingat.
                                </div>

                                <div class="form-group">
                                    <label>Password Saat Ini <span class="text-danger">*</span></label>
                                    <input type="password" wire:model="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password saat ini">
                                    @error('current_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" wire:model="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                    @error('new_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" wire:model="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="Ulangi password baru">
                                    @error('new_password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-key"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
