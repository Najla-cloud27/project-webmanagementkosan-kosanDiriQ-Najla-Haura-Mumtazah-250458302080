@php
    $header = 'Kelola Booking';
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
            <button wire:click="openModal" class="btn btn-primary btn-block">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
        <div class="col-md-3">
            <select wire:model.live="statusFilter" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                <option value="dikonfirmasi">Dikonfirmasi</option>
                <option value="selesai">Selesai</option>
                <option value="dibatalkan">Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-7">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode booking, penyewa, kamar...">
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Booking</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Booking</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Durasi</th>
                        <th>Total Harga</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $index => $booking)
                    <tr>
                        <td>{{ $bookings->firstItem() + $index }}</td>
                        <td><strong>{{ $booking->booking_code }}</strong></td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->room->name }}</td>
                        <td>{{ $booking->duration_in_months }} bulan</td>
                        <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        {{-- ⬇⬇ Tambahkan ini untuk menampilkan catatan --}}
                        <td>{{ $booking->notes, 40 ?? '-' }}</td>
                        <td>
                            @if($booking->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($booking->status == 'menunggu_verifikasi')
                                <span class="badge badge-info">Menunggu Verifikasi</span>
                            @elseif($booking->status == 'dikonfirmasi')
                                <span class="badge badge-success">Dikonfirmasi</span>
                            @elseif($booking->status == 'selesai')
                                <span class="badge badge-primary">Selesai</span>
                            @elseif($booking->status == 'dibatalkan')
                                <span class="badge badge-danger">Dibatalkan</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="viewDetail({{ $booking->id }})" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($booking->status == 'pending' || $booking->status == 'menunggu_verifikasi')
                                <button wire:click="updateStatus({{ $booking->id }}, 'dikonfirmasi')" class="btn btn-sm btn-success" title="Konfirmasi" onclick="return confirm('Konfirmasi booking ini? Tagihan akan otomatis dibuat.')">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                                <button wire:click="updateStatus({{ $booking->id }}, 'dibatalkan')" class="btn btn-sm btn-danger" title="Batalkan" onclick="return confirm('Batalkan booking ini?')">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            @endif
                            @if($booking->status == 'dikonfirmasi')
                                <button wire:click="updateStatus({{ $booking->id }}, 'selesai')" class="btn btn-sm btn-primary" title="Selesaikan">
                                    <i class="fas fa-check-double"></i> Selesai
                                </button>
                            @endif
                            <button wire:click="edit({{ $booking->id }})" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="delete({{ $booking->id }})" onclick="return confirm('Yakin hapus booking ini?')" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Tidak ada data booking
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $editMode ? 'Edit Booking' : 'Tambah Booking' }}</h4>
                    <button type="button" wire:click="closeModal" class="close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Penyewa <span class="text-danger">*</span></label>
                                    <select wire:model.live="user_id" class="form-control @error('user_id') is-invalid @enderror">
                                        <option value="">Pilih Penyewa</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kamar <span class="text-danger">*</span></label>
                                    <select wire:model.live="room_id" class="form-control @error('room_id') is-invalid @enderror">
                                        <option value="">Pilih Kamar</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" {{ $room_id == $room->id ? 'selected' : '' }}>
                                                {{ $room->name }} - Rp {{ number_format($room->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Durasi (Bulan) <span class="text-danger">*</span></label>
                                    <input type="number" wire:model.live="duration_in_months" class="form-control @error('duration_in_months') is-invalid @enderror" min="1" value="{{ $duration_in_months }}">
                                    @error('duration_in_months') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Check-in <span class="text-danger">*</span></label>
                                    <input type="date" wire:model.live="planned_check_in_date" class="form-control @error('planned_check_in_date') is-invalid @enderror" value="{{ $planned_check_in_date }}">
                                    @error('planned_check_in_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea wire:model="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"></textarea>
                            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
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

    <!-- Modal Detail -->
    @if($showDetailModal && $selectedBooking)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Booking</h4>
                    <button type="button" wire:click="closeDetailModal" class="close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Booking</th>
                            <td>{{ $selectedBooking->booking_code }}</td>
                        </tr>
                        <tr>
                            <th>Penyewa</th>
                            <td>{{ $selectedBooking->user->name }} ({{ $selectedBooking->user->email }})</td>
                        </tr>
                        <tr>
                            <th>Kamar</th>
                            <td>{{ $selectedBooking->room->name }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>{{ $selectedBooking->duration_in_months }} bulan</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp {{ number_format($selectedBooking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Check-in</th>
                            <td>{{ $selectedBooking->planned_check_in_date ? \Carbon\Carbon::parse($selectedBooking->planned_check_in_date)->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Check-out</th>
                            <td>{{ $selectedBooking->selesai_booking ? \Carbon\Carbon::parse($selectedBooking->selesai_booking)->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($selectedBooking->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($selectedBooking->status == 'menunggu_verifikasi')
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                @elseif($selectedBooking->status == 'dikonfirmasi')
                                    <span class="badge badge-success">Dikonfirmasi</span>
                                @elseif($selectedBooking->status == 'selesai')
                                    <span class="badge badge-primary">Selesai</span>
                                @elseif($selectedBooking->status == 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($selectedBooking->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($selectedBooking->bill)
                        <tr>
                            <th>Kode Tagihan</th>
                            <td>{{ $selectedBooking->bill->bill_code }}</td>
                        </tr>
                        <tr>
                            <th>Status Tagihan</th>
                            <td>
                                @if($selectedBooking->bill->status == 'belum_dibayar')
                                    <span class="badge badge-warning">Belum Dibayar</span>
                                @elseif($selectedBooking->bill->status == 'menunggu_verifikasi')
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                @elseif($selectedBooking->bill->status == 'dibayar')
                                    <span class="badge badge-success">Dibayar</span>
                                @else
                                    <span class="badge badge-danger">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $selectedBooking->notes ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Booking</th>
                            <td>{{ $selectedBooking->created_at->format('d M Y H:i') }}</td>
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
