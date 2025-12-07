@php
    $header = 'Kelola Tagihan';
@endphp

<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="icon fas fa-check"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Action & Filter -->
    <div class="row mb-3">
        <div class="col-md-2">
            <button wire:click="$set('showModal', true)" class="btn btn-primary btn-block">
                <i class="fas fa-plus"></i> Buat Tagihan
            </button>
        </div>
        <div class="col-md-3">
            <select wire:model.live="statusFilter" class="form-control">
                <option value="">Semua Status</option>
                <option value="belum_dibayar">Belum Dibayar</option>
                <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                <option value="dibayar">Dibayar</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>
        <div class="col-md-7">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode tagihan, nama penyewa...">
        </div>
    </div>

    <!-- Bills Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Tagihan</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Tagihan</th>
                        <th>Penyewa</th>
                        <th>Booking</th>
                        <th>Jumlah</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                    <tr>
                        <td>{{ $bills->firstItem() + $index }}</td>
                        <td><strong>{{ $bill->bill_code }}</strong></td>
                        <td>{{ $bill->user->name }}</td>
                        <td>{{ $bill->booking ? $bill->booking->booking_code : '-' }}</td>
                        <td>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</td>
                        <td>
                            @if($bill->status == 'belum_dibayar')
                                <span class="badge badge-warning">Belum Dibayar</span>
                            @elseif($bill->status == 'menunggu_verifikasi')
                                <span class="badge badge-info">Menunggu Verifikasi</span>
                            @elseif($bill->status == 'dibayar')
                                <span class="badge badge-success">Dibayar</span>
                            @else
                                <span class="badge badge-danger">Overdue</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="viewDetail({{ $bill->id }})" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($bill->status == 'belum_dibayar')
                                <button wire:click="delete({{ $bill->id }})" onclick="return confirm('Yakin hapus tagihan ini?')" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Tidak ada data tagihan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $bills->links() }}
        </div>
    </div>

    <!-- Modal Create Bill -->
    @if($showModal ?? false)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buat Tagihan Baru</h4>
                    <button type="button" wire:click="$set('showModal', false)" class="close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label>Booking <span class="text-danger">*</span></label>
                            <select wire:model="booking_id" class="form-control @error('booking_id') is-invalid @enderror">
                                <option value="">Pilih Booking</option>
                                @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}">{{ $booking->booking_code }} - {{ $booking->user->name }}</option>
                                @endforeach
                            </select>
                            @error('booking_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Total Tagihan <span class="text-danger">*</span></label>
                            <input type="number" wire:model="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Contoh: 1000000">
                            @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" wire:model="due_date" class="form-control @error('due_date') is-invalid @enderror">
                            @error('due_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea wire:model="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi tagihan"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-default">Batal</button>
                    <button type="button" wire:click="createBill" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Detail -->
    @if(($showDetailModal ?? false) && $selectedBill)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Tagihan</h4>
                    <button type="button" wire:click="closeDetailModal" class="close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Tagihan</th>
                            <td>{{ $selectedBill->bill_code }}</td>
                        </tr>
                        <tr>
                            <th>Penyewa</th>
                            <td>{{ $selectedBill->user->name }} ({{ $selectedBill->user->email }})</td>
                        </tr>
                        <tr>
                            <th>Booking</th>
                            <td>{{ $selectedBill->booking ? $selectedBill->booking->booking_code : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Total Tagihan</th>
                            <td><strong>Rp {{ number_format($selectedBill->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal Jatuh Tempo</th>
                            <td>{{ \Carbon\Carbon::parse($selectedBill->due_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($selectedBill->status == 'belum_dibayar')
                                    <span class="badge badge-warning">Belum Dibayar</span>
                                @elseif($selectedBill->status == 'menunggu_verifikasi')
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                @elseif($selectedBill->status == 'dibayar')
                                    <span class="badge badge-success">Dibayar</span>
                                @else
                                    <span class="badge badge-danger">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        @if($selectedBill->payment_date)
                        <tr>
                            <th>Tanggal Pembayaran</th>
                            <td>{{ \Carbon\Carbon::parse($selectedBill->payment_date)->format('d M Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $selectedBill->description ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>{{ $selectedBill->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeDetailModal" class="btn btn-default">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
