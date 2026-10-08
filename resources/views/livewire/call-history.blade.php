<div>
    <h4 class="mb-3">Riwayat &amp; Laporan Panggilan</h4>

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3"><input type="date" class="form-control" wire:model.live="from"></div>
        <div class="col-6 col-md-3"><input type="date" class="form-control" wire:model.live="to"></div>
        <div class="col-6 col-md-3">
            <select class="form-select" wire:model.live="roomId">
                <option value="">Semua kamar</option>
                @foreach ($rooms as $r) <option value="{{ $r->id }}">Kamar {{ $r->name }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" wire:model.live="level">
                <option value="">Semua level</option>
                <option value="normal">Biasa</option>
                <option value="emergency">Darurat</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mb-3 text-center">
        <div class="col-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Jumlah panggilan</div><div class="fs-3">{{ $stats['total'] }}</div>
        </div></div></div>
        <div class="col-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Rata-rata waktu respons</div><div class="fs-3">{{ $stats['avg'] }} dtk</div>
        </div></div></div>
        <div class="col-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Respons terlama</div><div class="fs-3">{{ $stats['max'] }} dtk</div>
        </div></div></div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Waktu panggilan</th><th>Kamar</th><th>Level</th><th>Status</th><th>Respons</th><th>Selesai</th></tr></thead>
                <tbody>
                @forelse ($calls as $c)
                    <tr wire:key="h-{{ $c->id }}">
                        <td>{{ $c->called_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $c->room->name }}</td>
                        <td><span class="badge bg-{{ $c->level === 'emergency' ? 'danger' : 'warning text-dark' }}">{{ $c->level === 'emergency' ? 'Darurat' : 'Biasa' }}</span></td>
                        <td>{{ ['waiting' => 'Menunggu', 'accepted' => 'Ditangani', 'completed' => 'Selesai'][$c->status] }}</td>
                        <td>{{ $c->response_seconds !== null ? $c->response_seconds . ' dtk' : '-' }}</td>
                        <td>{{ $c->completed_at?->format('H:i:s') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Tidak ada data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $calls->links() }}</div>
    </div>
</div>