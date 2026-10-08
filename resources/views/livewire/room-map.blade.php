<div wire:poll.3s class="room-page">

    <style>
        /* =========================
           MAIN PAGE
        ========================== */

        .room-page {
            background: #f3f5f9;
            min-height: calc(100vh - 56px);
            padding: 28px 20px 40px;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            color: #111827;
        }

        .room-wrap {
            max-width: 1280px;
            margin: 0 auto;
        }

        /* =========================
           HEADER
        ========================== */

        .room-header {
            background: #fff;
            border-radius: 12px;
            padding: 22px 26px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .room-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .room-header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .room-live {
            background: #e8f8ee;
            color: #15803d;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 13px;
            border-radius: 999px;

            display: flex;
            align-items: center;
            gap: 7px;
        }

        .room-live-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
        }

        /* =========================
           STATISTIK
        ========================== */

        .room-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .room-stat {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
            border-left: 4px solid;
        }

        .room-stat.normal {
            border-color: #22c55e;
        }

        .room-stat.calling {
            border-color: #ef4444;
        }

        .room-stat.offline {
            border-color: #9ca3af;
        }

        .room-stat-label {
            display: block;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .room-stat-number {
            display: block;
            font-size: 32px;
            font-weight: 800;
            margin-top: 7px;
        }

        .room-stat.normal .room-stat-number {
            color: #16a34a;
        }

        .room-stat.calling .room-stat-number {
            color: #dc2626;
        }

        .room-stat.offline .room-stat-number {
            color: #374151;
        }

        /* =========================
           ANTREAN PANGGILAN
        ========================== */

        .room-queue {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 30px;
        }

        .room-queue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .room-queue-title {
            margin: 0;
            color: #991b1b;
            font-size: 18px;
            font-weight: 800;
        }

        .room-queue-count {
            background: #dc2626;
            color: #fff;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .room-calls {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .room-call {
            background: #fff;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 16px 18px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .room-call-name {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .room-call-type {
            color: #6b7280;
            font-size: 12px;
        }

        .room-call-badge {
            display: inline-block;
            margin-top: 7px;
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .room-call-btn {
            border: 0;
            background: #dc2626;
            color: #fff;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .room-call-btn:hover {
            background: #b91c1c;
        }

        .room-call-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .room-empty {
            background: #fff;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }

        .room-empty-icon {
            font-size: 30px;
            margin-bottom: 8px;
        }

        /* =========================
           JUDUL STATUS KAMAR
        ========================== */

        .room-section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .room-section-title h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        .room-section-title span {
            color: #6b7280;
            font-size: 12px;
        }

        /* =========================
           LIST KAMAR
        ========================== */

        .room-list {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .room-card {
            background: #fff;
            border-radius: 12px;
            padding: 17px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .09);
        }

        .room-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 14px;
        }

        .room-name {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .room-type {
            display: block;
            color: #6b7280;
            font-size: 11px;
            margin-top: 3px;
        }

        .room-status {
            font-size: 10px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 5px;
            white-space: nowrap;
        }

        .room-status.normal {
            background: #dcfce7;
            color: #15803d;
        }

        .room-status.calling {
            background: #fee2e2;
            color: #dc2626;
        }

        .room-status.panggilan {
            background: #fee2e2;
            color: #dc2626;
        }

        .room-status.emergency {
            background: #fee2e2;
            color: #dc2626;
        }

        .room-status.offline {
            background: #f3f4f6;
            color: #6b7280;
        }

        .room-sensor {
            background: #f9fafb;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .room-sensor-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 12px;
        }

        .room-sensor-row span {
            color: #6b7280;
        }

        .room-sensor-row strong {
            color: #111827;
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 1100px) {
            .room-list {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 850px) {

            .room-stats {
                grid-template-columns: 1fr;
            }

            .room-list {
                grid-template-columns: repeat(2, 1fr);
            }

            .room-calls {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {

            .room-page {
                padding: 18px 12px 30px;
            }

            .room-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .room-list {
                grid-template-columns: 1fr;
            }

            .room-call {
                flex-direction: column;
                align-items: stretch;
            }

            .room-call-btn {
                width: 100%;
            }

            .room-section-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>


    <div class="room-wrap">

        {{-- =====================================================
             HEADER DASHBOARD
        ====================================================== --}}
        <div class="room-header">

            <div>
                <h1>
                    NurseCall &amp; CareRoom Dashboard
                </h1>

                <p>
                    Monitoring Kamar Rawat Inap &amp; Panggilan Darurat IoT
                </p>
            </div>

            <div class="room-live">
                <span class="room-live-dot"></span>
                Live System
            </div>

        </div>


        {{-- =====================================================
             STATISTIK
        ====================================================== --}}
        <div class="room-stats">

            {{-- Kamar Normal --}}
            <div class="room-stat normal">

                <span class="room-stat-label">
                    Kamar Normal
                </span>

                <span class="room-stat-number">
                    {{ $stats['normal'] ?? 0 }}
                </span>

            </div>


            {{-- Panggilan Aktif --}}
            <div class="room-stat calling">

                <span class="room-stat-label">
                    Panggilan Aktif / Emergency
                </span>

                <span class="room-stat-number">
                    {{ $stats['calling'] ?? 0 }}
                </span>

            </div>


            {{-- Offline --}}
            <div class="room-stat offline">

                <span class="room-stat-label">
                    Kamar Offline
                </span>

                <span class="room-stat-number">
                    {{ $stats['offline'] ?? 0 }}
                </span>

            </div>

        </div>


        {{-- =====================================================
             ANTREAN PANGGILAN DARURAT
        ====================================================== --}}
        <div class="room-queue">

            <div class="room-queue-header">

                <h2 class="room-queue-title">
                    🚨 Antrean Panggilan Darurat Aktif
                </h2>

                @if(isset($activeCalls) && count($activeCalls) > 0)

                    <span class="room-queue-count">
                        {{ count($activeCalls) }} Panggilan
                    </span>

                @endif

            </div>


            @if(isset($activeCalls) && count($activeCalls) > 0)

                <div class="room-calls">

                    @foreach($activeCalls as $call)

                        <div
                            class="room-call"
                            wire:key="room-call-{{ $call['id'] ?? $call->id }}"
                        >

                            <div>

                                <div class="room-call-name">

                                    @if(isset($call['name']))
                                        {{ $call['name'] }}
                                    @elseif(isset($call->room))
                                        {{ $call->room->name }}
                                    @else
                                        Kamar
                                    @endif

                                </div>


                                <div class="room-call-type">

                                    @if(isset($call['room_code']))

                                        Kode Kamar:
                                        {{ $call['room_code'] }}

                                    @elseif(isset($call->room))

                                        {{ $call->room->type ?? '' }}

                                    @endif

                                </div>


                                <span class="room-call-badge">

                                    @if(isset($call['status']))
                                        {{ $call['status'] }}
                                    @elseif(isset($call->status))
                                        {{ $call->status }}
                                    @else
                                        CALLING
                                    @endif

                                </span>

                            </div>


                            {{-- Tombol selesai --}}
                            @if(isset($call['id']) || isset($call->id))

                                <button
                                    type="button"
                                    class="room-call-btn"
                                    wire:click="finish({{ $call['id'] ?? $call->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    Selesaikan Panggilan
                                </button>

                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                <div class="room-empty">

                    <div class="room-empty-icon">
                        🛡️
                    </div>

                    Tidak ada panggilan darurat aktif saat ini.

                </div>

            @endif

        </div>


        {{-- =====================================================
             STATUS SELURUH KAMAR
        ====================================================== --}}
        <div class="room-section-title">

            <h2>
                Status Seluruh Kamar
            </h2>

            <span>
                Diperbarui otomatis setiap 3 detik
            </span>

        </div>


        <div class="room-list">

            @forelse($rooms as $room)

                @php

                    /*
                     * Mengambil status kamar.
                     * Mendukung beberapa kemungkinan nama
                     * property dari component/database.
                     */

                    $status = $room->state
                        ?? $room->status
                        ?? 'offline';

                    $statusClass = strtolower($status);

                    if ($statusClass === 'panggilan') {
                        $statusClass = 'panggilan';
                    }

                @endphp


                <div
                    class="room-card"
                    wire:key="room-card-{{ $room->id ?? $room['id'] ?? $loop->index }}"
                >

                    {{-- Header kartu kamar --}}
                    <div class="room-card-head">

                        <div>

                            <div class="room-name">

                                {{ $room->name ?? $room['name'] ?? 'Kamar' }}

                            </div>


                            <span class="room-type">

                                {{ $room->type ?? $room['type'] ?? '' }}

                            </span>

                        </div>


                        {{-- Status --}}
                        <span class="room-status {{ $statusClass }}">

                            @if($statusClass === 'normal')

                                Normal

                            @elseif(
                                $statusClass === 'calling' ||
                                $statusClass === 'panggilan' ||
                                $statusClass === 'emergency'
                            )

                                Panggilan

                            @else

                                Offline

                            @endif

                        </span>

                    </div>


                    {{-- Sensor --}}
                    <div class="room-sensor">

                        <div class="room-sensor-row">

                            <span>
                                Suhu:
                            </span>

                            <strong>

                                {{
                                    number_format(
                                        $room->temp
                                        ?? $room->temperature
                                        ?? $room['temp']
                                        ?? $room['temperature']
                                        ?? 0,
                                        1
                                    )
                                }}°C

                            </strong>

                        </div>


                        <div class="room-sensor-row">

                            <span>
                                Kelembapan:
                            </span>

                            <strong>

                                {{
                                    round(
                                        $room->hum
                                        ?? $room->humidity
                                        ?? $room['hum']
                                        ?? $room['humidity']
                                        ?? 0
                                    )
                                }}%

                            </strong>

                        </div>

                    </div>

                </div>


            @empty

                {{-- Jika belum ada kamar --}}
                <div
                    style="
                        grid-column: 1 / -1;
                        background: #fff;
                        border-radius: 12px;
                        padding: 40px 20px;
                        text-align: center;
                        color: #6b7280;
                        box-shadow: 0 1px 3px rgba(0,0,0,.08);
                    "
                >

                    <div
                        style="
                            font-size: 36px;
                            margin-bottom: 10px;
                        "
                    >
                        🏥
                    </div>

                    <strong
                        style="
                            display: block;
                            color: #374151;
                            font-size: 15px;
                            margin-bottom: 5px;
                        "
                    >
                        Belum ada kamar
                    </strong>

                    <span style="font-size: 13px;">
                        Jalankan seeder untuk menampilkan data kamar.
                    </span>

                </div>

            @endforelse

        </div>

    </div>

</div>