<div wire:poll.3s class="nc">
    <style>
        .nc { background:#f3f5f9; min-height:100vh; padding:24px 16px; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; color:#111827; }
        .nc-wrap { max-width:1280px; margin:0 auto; }
        .nc-card { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .nc-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; margin-bottom:24px; }
        .nc-header h1 { margin:0; font-size:24px; font-weight:800; }
        .nc-header p { margin:4px 0 0; font-size:14px; color:#6b7280; }
        .nc-live { background:#e8f8ee; color:#15803d; font-size:12px; font-weight:600; padding:6px 12px; border-radius:999px; display:flex; align-items:center; gap:6px; }
        .nc-live i { width:8px; height:8px; background:#22c55e; border-radius:50%; display:inline-block; }
        .nc-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
        .nc-stat { padding:20px 24px; border-left:4px solid; }
        .nc-stat small { font-size:12px; font-weight:700; letter-spacing:.03em; color:#374151; text-transform:uppercase; }
        .nc-stat b { display:block; font-size:32px; margin-top:8px; }
        .nc-stat.green { border-color:#22c55e; } .nc-stat.green b { color:#16a34a; }
        .nc-stat.red { border-color:#ef4444; }   .nc-stat.red b { color:#dc2626; }
        .nc-stat.gray { border-color:#9ca3af; }  .nc-stat.gray b { color:#374151; }
        .nc-queue { background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:20px 24px; margin-bottom:32px; }
        .nc-queue h2 { margin:0 0 16px; font-size:18px; color:#7f1d1d; }
        .nc-queue-list { display:flex; flex-wrap:wrap; gap:16px; }
        .nc-call { background:#fff; border:1px solid #fecaca; border-radius:10px; padding:16px 18px; display:flex; align-items:center; justify-content:space-between; gap:24px; min-width:400px; }
        .nc-call strong { display:block; margin-bottom:8px; }
        .nc-badge { display:inline-block; background:#dc2626; color:#fff; font-size:12px; font-weight:700; padding:4px 10px; border-radius:4px; text-transform:uppercase; }
        .nc-btn { background:#dc2626; color:#fff; border:0; border-radius:6px; padding:10px 16px; font-weight:700; font-size:13px; cursor:pointer; }
        .nc-btn:hover { background:#b91c1c; }
        .nc-title { font-size:18px; font-weight:800; margin:0 0 16px; }
        .nc-rooms { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .nc-room { padding:16px; }
        .nc-room-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .nc-room-head strong { font-size:14px; }
        .nc-tag { font-size:11px; font-weight:700; padding:3px 10px; border-radius:4px; }
        .nc-tag.normal { background:#dcfce7; color:#15803d; }
        .nc-tag.panggilan, .nc-tag.emergency, .nc-tag.calling { background:#fee2e2; color:#dc2626; }
        .nc-tag.offline { background:#f3f4f6; color:#6b7280; }
        .nc-sensor { background:#f9fafb; border-radius:8px; padding:10px 12px; font-size:12px; }
        .nc-sensor div { display:flex; justify-content:space-between; padding:4px 0; color:#6b7280; }
        .nc-sensor b { color:#111827; }
        @media (max-width:1024px) { .nc-rooms { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:640px) {
            .nc-stats, .nc-rooms { grid-template-columns:1fr; }
            .nc-call { min-width:0; width:100%; }
        }
    </style>

    @php
        $hasDbRooms = isset($rooms) && count($rooms) > 0;
        
        $displayRooms = $hasDbRooms ? $rooms : collect([
            (object)['id' => 1, 'name' => 'Kamar 101', 'type' => 'VVIP', 'state' => 'normal', 'temp' => 24.5, 'hum' => 55],
            (object)['id' => 2, 'name' => 'Kamar 102', 'type' => 'VIP', 'state' => 'panggilan', 'temp' => 26.1, 'hum' => 60],
            (object)['id' => 3, 'name' => 'Kamar 103', 'type' => 'Kelas 1', 'state' => 'panggilan', 'temp' => 28.0, 'hum' => 65],
            (object)['id' => 4, 'name' => 'Kamar 104', 'type' => 'Kelas 1', 'state' => 'normal', 'temp' => 23.8, 'hum' => 50],
            (object)['id' => 5, 'name' => 'Kamar 105', 'type' => 'Kelas 2', 'state' => 'offline', 'temp' => 0, 'hum' => 0],
        ]);

        $hasDbCalls = isset($activeCalls) && count($activeCalls) > 0;
        $displayCalls = $hasDbCalls ? $activeCalls : ($hasDbRooms ? collect([]) : collect([
            (object)['id' => 1, 'status' => 'EMERGENCY', 'room' => (object)['name' => 'Kamar 102', 'type' => 'VIP']],
            (object)['id' => 2, 'status' => 'CALLING', 'room' => (object)['name' => 'Kamar 103', 'type' => 'Kelas 1']],
        ]));

        $normCount = 0;
        $offCount = 0;
        foreach ($displayRooms as $r) {
            $st = strtolower(is_object($r) ? ($r->state ?? $r->status ?? '') : ($r['state'] ?? $r['status'] ?? ''));
            if ($st === 'normal') $normCount++;
            if ($st === 'offline') $offCount++;
        }

        $displayNormal = $hasDbRooms ? ($normal ?? $normCount) : $normCount;
        $displayActive = count($displayCalls);
        $displayOffline = $hasDbRooms ? ($offline ?? $offCount) : $offCount;
    @endphp

    <div class="nc-wrap">
        {{-- Header --}}
        <div class="nc-card nc-header">
            <div>
                <h1>NurseCall &amp; CareRoom Dashboard</h1>
                <p>Monitoring Kamar Rawat Inap &amp; Panggilan Darurat IoT</p>
            </div>
            <span class="nc-live"><i></i> Live System</span>
        </div>

        {{-- Statistik --}}
        <div class="nc-stats">
            <div class="nc-card nc-stat green"><small>Kamar Normal</small><b>{{ $displayNormal }}</b></div>
            <div class="nc-card nc-stat red"><small>Panggilan Active / Emergency</small><b>{{ $displayActive }}</b></div>
            <div class="nc-card nc-stat gray"><small>Kamar Offline</small><b>{{ $displayOffline }}</b></div>
        </div>

        {{-- Antrean panggilan --}}
        @if (count($displayCalls) > 0)
            <div class="nc-queue">
                <h2>🚨 Antrean Panggilan Darurat Aktif</h2>
                <div class="nc-queue-list">
                    @foreach ($displayCalls as $call)
                        @php
                            $cId = is_object($call) ? ($call->id ?? $loop->index) : ($call['id'] ?? $loop->index);
                            $cStatus = is_object($call) ? ($call->status ?? 'EMERGENCY') : ($call['status'] ?? 'EMERGENCY');
                            $cRoom = is_object($call) ? ($call->room ?? null) : ($call['room'] ?? null);
                            $cRoomName = is_object($cRoom) ? ($cRoom->name ?? 'Kamar') : ($cRoom['name'] ?? 'Kamar');
                            $cRoomType = is_object($cRoom) ? ($cRoom->type ?? '') : ($cRoom['type'] ?? '');
                        @endphp
                        <div class="nc-call" wire:key="call-{{ $cId }}">
                            <div>
                                <strong>{{ $cRoomName }} @if($cRoomType)({{ $cRoomType }})@endif</strong>
                                <span class="nc-badge">{{ $cStatus }}</span>
                            </div>
                            <button class="nc-btn" wire:click="finish({{ $cId }})" wire:loading.attr="disabled">
                                Selesaikan Panggilan
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Status seluruh kamar --}}
        <h2 class="nc-title">Status Seluruh Kamar</h2>
        <div class="nc-rooms">
            @foreach ($displayRooms as $room)
                @php
                    $rId = is_object($room) ? ($room->id ?? $loop->index) : ($room['id'] ?? $loop->index);
                    $rName = is_object($room) ? ($room->name ?? 'Kamar') : ($room['name'] ?? 'Kamar');
                    $rType = is_object($room) ? ($room->type ?? '') : ($room['type'] ?? '');
                    $rState = strtolower(is_object($room) ? ($room->state ?? $room->status ?? 'normal') : ($room['state'] ?? $room['status'] ?? 'normal'));
                    $rTemp = is_object($room) ? ($room->temp ?? $room->temperature ?? 0) : ($room['temp'] ?? $room['temperature'] ?? 0);
                    $rHum = is_object($room) ? ($room->hum ?? $room->humidity ?? 0) : ($room['hum'] ?? $room['humidity'] ?? 0);
                @endphp
                <div class="nc-card nc-room" wire:key="room-{{ $rId }}">
                    <div class="nc-room-head">
                        <strong>{{ $rName }} @if($rType)({{ $rType }})@endif</strong>
                        <span class="nc-tag {{ $rState }}">{{ ucfirst($rState) }}</span>
                    </div>
                    <div class="nc-sensor">
                        <div><span>Suhu:</span><b>{{ number_format((float)$rTemp, 1) }}°C</b></div>
                        <div><span>Kelembapan:</span><b>{{ round((float)$rHum) }}%</b></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>