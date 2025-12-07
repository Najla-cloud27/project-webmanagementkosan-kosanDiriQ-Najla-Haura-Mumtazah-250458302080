@php
    $header = 'Kelola Kamar';
@endphp

<div>
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <button wire:click="openModal" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kamar
            </button>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kamar</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kamar...">
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
                                <th>Foto</th>
                                <th>Nama Kamar</th>
                                <th>Deskripsi</th>
                                <th>Ukuran</th>
                                <th>Harga/Bulan</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $index => $room)
                            <tr>
                                <td>{{ $rooms->firstItem() + $index }}</td>
                                <td>
                                    @if($room->main_image_url)
                                        <img src="{{ Storage::url($room->main_image_url) }}" 
                                             alt="{{ $room->name }}"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px; border-radius: 4px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{ $room->name }}</strong></td>
                                <td>{{ Str::limit(strip_tags($room->description), 40) }}</td>
                                <td>{{ $room->size }}</td>
                                <td>Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                                <td>{{ $room->stok }}</td>
                                <td>
                                    @if($room->status == 'tersedia')
                                        <span class="badge badge-success">Tersedia</span>
                                    @elseif($room->status == 'terisi')
                                        <span class="badge badge-danger">Terisi</span>
                                    @elseif($room->status == 'perawatan')
                                        <span class="badge badge-warning">Perawatan</span>
                                    @else
                                        <span class="badge badge-info">Sudah Dipesan</span>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="edit({{ $room->id }})" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $room->id }})" 
                                            onclick="return confirm('Yakin ingin menghapus kamar ini?')"
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i> Tidak ada data kamar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $rooms->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $editMode ? 'Edit Kamar' : 'Tambah Kamar' }}</h4>
                    <button type="button" wire:click="closeModal" class="close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form>
                        {{-- Upload Single Image - PINDAH KE ATAS --}}
                        <div class="form-group">
                            <label>Upload Foto Kamar</label>
                            <input type="file" 
                                   wire:model="image" 
                                   accept="image/*"
                                   class="form-control-file @error('image') is-invalid @enderror">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF. Max 2MB.
                            </small>
                            @error('image') 
                                <span class="invalid-feedback d-block">{{ $message }}</span> 
                            @enderror
                            
                            {{-- Loading Indicator --}}
                            <div wire:loading wire:target="image" class="text-info mt-2">
                                <i class="fas fa-spinner fa-spin"></i> Uploading...
                            </div>
                            
                            {{-- Preview New Image --}}
                            @if($image)
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" 
                                     class="img-thumbnail"
                                     style="max-width: 200px; max-height: 150px; object-fit: cover;">
                                <small class="text-success d-block mt-1">
                                    <i class="fas fa-check-circle"></i> Preview Foto Baru
                                </small>
                            </div>
                            @endif
                        </div>

                        {{-- Current Image (Edit Mode) --}}
                        @if($editMode && $currentImage)
                        <div class="form-group">
                            <label>Foto Saat Ini:</label>
                            <div class="position-relative d-inline-block">
                                <img src="{{ Storage::url($currentImage) }}" 
                                     class="img-thumbnail"
                                     style="max-width: 200px; max-height: 150px; object-fit: cover;"
                                     alt="Current Room Image">
                                <button type="button" 
                                        wire:click="deleteImage"
                                        wire:confirm="Yakin ingin menghapus foto ini?"
                                        class="btn btn-danger btn-sm position-absolute"
                                        style="top: 5px; right: 5px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Upload foto baru untuk mengganti
                            </small>
                        </div>
                        @endif

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Kamar <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kamar A-101">
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga per Bulan <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="price" class="form-control @error('price') is-invalid @enderror" placeholder="Contoh: 1000000">
                                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ukuran (m²)</label>
                                    <input type="text" wire:model="size" class="form-control @error('size') is-invalid @enderror" placeholder="Contoh: 3x4">
                                    @error('size') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stok <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="stok" class="form-control @error('stok') is-invalid @enderror">
                                    @error('stok') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select wire:model="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="tersedia">Tersedia</option>
                                        <option value="terisi">Terisi</option>
                                        <option value="perawatan">Perawatan</option>
                                        <option value="sudah_dipesan">Sudah Dipesan</option>
                                    </select>
                                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group" wire:ignore>
                            <label>Deskripsi</label>
                            <textarea id="summernote-description" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi singkat tentang kamar">{{ $description }}</textarea>
                            @error('description') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fasilitas</label>
                            <textarea wire:model="fasilitas" rows="2" class="form-control @error('fasilitas') is-invalid @enderror" placeholder="Contoh: AC, WiFi, Kasur, Lemari, Kamar Mandi Dalam"></textarea>
                            @error('fasilitas') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" wire:click="closeModal" class="btn btn-default">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" 
                            wire:click="save" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                            class="btn btn-primary">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save"></i> {{ $editMode ? 'Update' : 'Simpan' }}
                        </span>
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    document.addEventListener('livewire:initialized', function() {
        initSummernote();
        
        // Reinitialize Summernote when modal opens
        Livewire.on('modal-opened', function() {
            setTimeout(() => {
                initSummernote();
            }, 100);
        });
        
        // Clean up when modal closes
        Livewire.on('modal-closed', function() {
            if ($('#summernote-description').summernote('codeview.isActivated')) {
                $('#summernote-description').summernote('codeview.deactivate');
            }
            $('#summernote-description').summernote('destroy');
        });
    });
    
    function initSummernote() {
        if ($('#summernote-description').length) {
            // Destroy existing instance if any
            if ($('#summernote-description').hasClass('note-editor')) {
                $('#summernote-description').summernote('destroy');
            }
            
            // Initialize Summernote
            $('#summernote-description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Tulis deskripsi kamar dengan detail...',
                callbacks: {
                    onChange: function(contents, $editable) {
                        @this.set('description', contents);
                    },
                    onInit: function() {
                        // Set initial value from Livewire
                        const initialValue = @this.get('description') || '';
                        $('#summernote-description').summernote('code', initialValue);
                    }
                }
            });
        }
    }
</script>
@endpush
