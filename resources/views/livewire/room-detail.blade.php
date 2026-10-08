<div wire:poll.3s>
    <!-- Navigation & Status Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('rooms') }}" wire:navigate class="btn btn-outline-secondary btn-sm mb-2">&larr; Kembali ke Peta Kamar</a>
            <h3 class="fw-bold mb-0">Detail {{ $room->name ?? 'Kamar' }}</h3>
        </div>
        <div>
            @if($isComfortable ?? false)
                <span class="badge bg-success fs-6 px-3 py-2">Kondisi Ruangan: NYAMAN</span>
            @else
                <span class="badge bg-danger fs-6 px-3 py-2">Kondisi Ruangan: DI LUAR BATAS</span>
            @endif
        </div>
    </div>

    <!-- Ringkasan Sensor -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <span class="text-muted small d-block mb-1">Suhu Ruangan Saat Ini</span>
                    <h2 class="fw-bold mb-0 text-primary">{{ number_format($room->temperature ?? 0, 1) }}°C</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <span class="text-muted small d-block mb-1">Kelembapan Udara Saat Ini</span>
                    <h2 class="fw-bold mb-0 text-info">{{ $room->humidity ?? 0 }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Grafik (Ditambahkan wire:ignore agar tidak hilang saat polling) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body" wire:ignore>
            <h5 class="fw-bold mb-3">Grafik Tren Suhu & Kelembapan (Wokwi Sensor)</h5>
            <div style="height: 320px;">
                <canvas id="sensorChart"
                    data-labels='@json($timeLabels)'
                    data-temp='@json($tempData)'
                    data-humidity='@json($humidityData)'>
                </canvas>
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderChart() {
            const ctx = document.getElementById('sensorChart');
            if (!ctx) return;

            // Hapus instance chart lama jika ada agar canvas tidak bentrok
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const labels = JSON.parse(ctx.getAttribute('data-labels') || '[]');
            const tempData = JSON.parse(ctx.getAttribute('data-temp') || '[]');
            const humData = JSON.parse(ctx.getAttribute('data-humidity') || '[]');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Suhu (°C)',
                            data: tempData,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Kelembapan (%)',
                            data: humData,
                            borderColor: '#0dcaf0',
                            backgroundColor: 'rgba(13, 202, 240, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { display: true, text: 'Suhu (°C)' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Kelembapan (%)' }
                        }
                    }
                }
            });
        }

        // Eksekusi render saat navigasi Livewire maupun reload biasa
        document.addEventListener('livewire:navigated', renderChart);
        document.addEventListener('DOMContentLoaded', renderChart);
    </script>
</div>