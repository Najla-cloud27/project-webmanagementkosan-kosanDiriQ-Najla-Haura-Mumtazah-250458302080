@php
    $header = 'Export Data';
@endphp

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Export Data ke Excel</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Export Laporan Pembayaran -->
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h5>Export Laporan Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Dari Tanggal</label>
                                        <input type="date" wire:model="dateFrom" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Sampai Tanggal</label>
                                        <input type="date" wire:model="dateTo" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select wire:model="billStatus" class="form-control">
                                            <option value="">Semua Status</option>
                                            <option value="belum_dibayar">Belum Dibayar</option>
                                            <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                                            <option value="dibayar">Dibayar</option>
                                            <option value="overdue">Overdue</option>
                                        </select>
                                    </div>
                                    <button wire:click="exportBills" class="btn btn-success btn-block">
                                        <i class="fas fa-file-excel"></i> Export Laporan Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Export Data Kamar -->
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h5>Export Data Kamar</h5>
                                </div>
                                <div class="card-body">
                                    <p>Export semua data kamar termasuk nama, harga, fasilitas, dan status.</p>
                                    <button wire:click="exportRooms" class="btn btn-info btn-block mt-5">
                                        <i class="fas fa-file-excel"></i> Export Data Kamar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
