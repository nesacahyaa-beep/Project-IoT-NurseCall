<div>
    <h4 class="mb-3">Manajemen Stok Barang Medis</h4>

    @if (session('ok')) 
        <div class="alert alert-success py-2">{{ session('ok') }}</div> 
    @endif

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Stok</th>
                        <th>Minimum</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    <tr wire:key="item-{{ $item->id }}" class="{{ $item->needs_reorder ? 'table-danger' : '' }}">
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->stock }} {{ $item->unit }}</td>
                        <td>{{ $item->min_stock }}</td>
                        <td>
                            @if ($item->needs_reorder)
                                <span class="badge bg-danger">Perlu Reorder</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">Belum ada barang.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Catat pemakaian --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Catat pemakaian</div>
                <div class="card-body">
                    <select class="form-select mb-2" wire:model="useItem">
                        <option value="">— pilih barang —</option>
                        @foreach ($items as $i) 
                            <option value="{{ $i->id }}">{{ $i->name }} ({{ $i->stock }})</option> 
                        @endforeach
                    </select>
                    @error('useItem') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <input type="number" min="1" class="form-control mb-2" placeholder="Jumlah" wire:model="useQty">
                    @error('useQty') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <select class="form-select mb-2" wire:model="useCall">
                        <option value="">Tidak dikaitkan ke panggilan</option>
                        @foreach ($calls as $c)
                            <option value="{{ $c->id }}">#{{ $c->id }} · Kamar {{ $c->room->name ?? '-' }} · {{ $c->called_at ? $c->called_at->format('d/m H:i') : '' }}</option>
                        @endforeach
                    </select>

                    <input type="text" class="form-control mb-2" placeholder="Catatan (opsional)" wire:model="useNote">
                    <button type="button" class="btn btn-primary w-100" wire:click="recordUsage">Simpan pemakaian</button>
                </div>
            </div>
        </div>

        {{-- Terima barang --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Terima barang (tambah stok)</div>
                <div class="card-body">
                    <select class="form-select mb-2" wire:model="inItem">
                        <option value="">— pilih barang —</option>
                        @foreach ($items as $i) 
                            <option value="{{ $i->id }}">{{ $i->name }}</option> 
                        @endforeach
                    </select>
                    @error('inItem') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <input type="number" min="1" class="form-control mb-2" placeholder="Jumlah" wire:model="inQty">
                    @error('inQty') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <input type="text" class="form-control mb-2" placeholder="Catatan (mis. nomor faktur)" wire:model="inNote">
                    <button type="button" class="btn btn-success w-100" wire:click="receiveStock">Tambah stok</button>
                </div>
            </div>
        </div>

        {{-- Tambah jenis barang baru --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Tambah jenis barang</div>
                <div class="card-body">
                    <form wire:submit="addItem">
                        <input type="text" class="form-control mb-2" placeholder="Nama barang" wire:model="name">
                        @error('name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Satuan" wire:model="unit">
                                @error('unit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col">
                                <input type="number" min="0" class="form-control" placeholder="Stok awal" wire:model="stock">
                                @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col">
                                <input type="number" min="0" class="form-control" placeholder="Minimum" wire:model="minStock">
                                @error('minStock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Tambah barang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Riwayat pemakaian &amp; penerimaan (15 terakhir)</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Panggilan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($usages as $u)
                    <tr wire:key="u-{{ $u->id }}">
                        <td>{{ $u->created_at ? $u->created_at->format('d/m H:i') : '-' }}</td>
                        <td>{{ $u->item->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $u->type === 'out' ? 'warning text-dark' : 'success' }}">
                                {{ $u->type === 'out' ? 'Pemakaian' : 'Penerimaan' }}
                            </span>
                        </td>
                        <td>{{ $u->quantity }}</td>
                        <td>{{ $u->call ? '#' . $u->call->id . ' · Kamar ' . ($u->call->room->name ?? '-') : '-' }}</td>
                        <td>{{ $u->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Belum ada riwayat.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>