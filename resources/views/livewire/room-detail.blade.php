<div>
    <span wire:poll.5s></span>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            Kamar {{ $room->name }}
            <span class="badge bg-{{ $room->status_theme }}">{{ $room->status_label }}</span>
        </h3>
        <a href="{{ route('rooms') }}" wire:navigate class="btn btn-outline-secondary btn-sm">← Peta Kamar</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Suhu</div>
            <div class="fs-3">{{ $room->temperature !== null ? number_format($room->temperature, 1) . ' °C' : '-' }}</div>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Kelembapan</div>
            <div class="fs-3">{{ $room->humidity !== null ? number_format($room->humidity, 0) . ' %' : '-' }}</div>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Kondisi ruangan</div>
            @if ($room->is_comfortable === null)
                <div class="fs-3">-</div>
            @else
                <div class="fs-3 text-{{ $room->is_comfortable ? 'success' : 'danger' }}">
                    {{ $room->is_comfortable ? 'Nyaman' : 'Di luar rentang' }}
                </div>
            @endif
        </div></div></div>
    </div>

    <div class="card mb-3">
        <div class="card-body" wire:ignore
             x-data="roomChart(@js($room->id), @js(config('nursecall.temp_range')), @js(config('nursecall.humidity_range')))">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Grafik suhu &amp; kelembapan (garis putus-putus = batas normal)</h6>
                <select class="form-select form-select-sm w-auto" x-model.number="hours" @change="load()">
                    <option value="1">1 jam</option>
                    <option value="6">6 jam</option>
                    <option value="24">24 jam</option>
                </select>
            </div>
            <div style="height: 300px"><canvas x-ref="canvas"></canvas></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Riwayat panggilan kamar ini</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>Waktu</th><th>Level</th><th>Status</th><th>Respons</th></tr></thead>
                <tbody>
                @forelse ($calls as $c)
                    <tr>
                        <td>{{ $c->called_at->format('d/m H:i:s') }}</td>
                        <td><span class="badge bg-{{ $c->level === 'emergency' ? 'danger' : 'warning text-dark' }}">{{ $c->level === 'emergency' ? 'Darurat' : 'Biasa' }}</span></td>
                        <td>{{ ['waiting' => 'Menunggu', 'accepted' => 'Ditangani', 'completed' => 'Selesai'][$c->status] }}</td>
                        <td>{{ $c->response_seconds !== null ? $c->response_seconds . ' dtk' : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Belum ada panggilan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>