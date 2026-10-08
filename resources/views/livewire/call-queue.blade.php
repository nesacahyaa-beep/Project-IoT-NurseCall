<div>
    {{-- Polling dimatikan saat form "Selesai" terbuka agar isian tidak terganggu --}}
    @unless ($completingId)
        <span wire:poll.3s></span>
    @endunless

    <h4 class="mb-3">Antrean Panggilan <span class="badge bg-dark">{{ $calls->count() }}</span></h4>

    @forelse ($calls as $call)
        @php $emergency = $call->level === 'emergency'; @endphp
        <div class="card mb-2 border-2 border-{{ $emergency ? 'danger' : 'warning' }}" wire:key="call-{{ $call->id }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong class="fs-5">Kamar {{ $call->room->name }}</strong>
                        <span class="badge bg-{{ $emergency ? 'danger' : 'warning text-dark' }}">
                            {{ $emergency ? 'DARURAT' : 'Biasa' }}
                        </span>

                        @if ($call->status === 'waiting')
                            <div class="text-muted"
                                 x-data="{ s: {{ (int) $call->called_at->diffInSeconds(now()) }} }"
                                 x-init="setInterval(() => s++, 1000)">
                                Sudah menunggu
                                <strong x-text="String(Math.floor(s/60)).padStart(2,'0') + ':' + String(s%60).padStart(2,'0')"></strong>
                            </div>
                        @else
                            <div class="text-muted">Sedang ditangani · respons {{ $call->response_seconds }} dtk</div>
                        @endif
                    </div>

                    <div>
                        @if ($call->status === 'waiting')
                            <button class="btn btn-primary" wire:click="accept({{ $call->id }})">Terima</button>
                        @elseif ($completingId !== $call->id)
                            <button class="btn btn-success" wire:click="startComplete({{ $call->id }})">Selesai</button>
                        @endif
                    </div>
                </div>

                @if ($completingId === $call->id)
                    <hr>
                    <div class="fw-semibold mb-2">Barang terpakai (opsional)</div>

                    @foreach ($usage as $i => $row)
                        <div class="row g-2 mb-2" wire:key="usage-{{ $i }}">
                            <div class="col-7">
                                <select class="form-select" wire:model="usage.{{ $i }}.item_id">
                                    <option value="">— pilih barang —</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} (stok {{ $item->stock }} {{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" min="1" class="form-control" wire:model="usage.{{ $i }}.quantity">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-outline-danger w-100" wire:click="removeRow({{ $i }})">✕</button>
                            </div>
                        </div>
                    @endforeach

                    @error('quantity') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror

                    <button class="btn btn-sm btn-outline-secondary" wire:click="addRow">+ Tambah barang</button>
                    <div class="mt-3">
                        <button class="btn btn-success" wire:click="confirmComplete">Simpan &amp; Selesai</button>
                        <button class="btn btn-link" wire:click="cancelComplete">Batal</button>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-light border">Tidak ada panggilan aktif.</div>
    @endforelse
</div>