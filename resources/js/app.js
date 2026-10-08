import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;
window.Chart = Chart;

document.addEventListener('alpine:init', () => {

    // ---------- Indikator koneksi dashboard ----------
    Alpine.data('connection', () => ({
        online: true,
        init() {
            this.ping();
            setInterval(() => this.ping(), 5000);
            window.addEventListener('offline', () => (this.online = false));
        },
        async ping() {
            try {
                const ctrl = new AbortController();
                const t = setTimeout(() => ctrl.abort(), 4000);
                const res = await fetch('/up', { cache: 'no-store', signal: ctrl.signal });
                clearTimeout(t);
                this.online = res.ok;
            } catch (e) {
                this.online = false;
            }
        },
    }));

    // ---------- Alarm suara + judul tab berkedip ----------
    Alpine.data('alarm', () => ({
        enabled: false, waiting: 0, emergency: 0, mode: null,
        ctx: null, soundTimer: null, titleTimer: null, baseTitle: document.title,

        enable() {
            this.ctx = new (window.AudioContext || window.webkitAudioContext)();
            this.ctx.resume();
            this.enabled = true;
            this.mode = null; // paksa sinkron ulang
            this.sync();
        },
        disable() {
            this.enabled = false;
            clearInterval(this.soundTimer);
            this.soundTimer = null;
            this.mode = null;
        },
        update(d) {
            this.waiting = d.waiting;
            this.emergency = d.emergency;
            this.sync();
        },
        sync() {
            const mode = this.waiting === 0 ? 'off' : (this.emergency > 0 ? 'emergency' : 'normal');
            if (mode === this.mode) return; // jangan restart tiap polling
            this.mode = mode;

            clearInterval(this.soundTimer);
            this.soundTimer = null;
            if (this.enabled && mode !== 'off') {
                const freq = mode === 'emergency' ? 1000 : 700;
                const every = mode === 'emergency' ? 600 : 1500;
                this.beep(freq);
                this.soundTimer = setInterval(() => this.beep(freq), every);
            }
            this.blinkTitle(mode !== 'off');
        },
        beep(freq) {
            const o = this.ctx.createOscillator();
            const g = this.ctx.createGain();
            o.type = 'square';
            o.frequency.value = freq;
            g.gain.value = 0.15;
            o.connect(g);
            g.connect(this.ctx.destination);
            o.start();
            o.stop(this.ctx.currentTime + 0.25);
        },
        blinkTitle(active) {
            clearInterval(this.titleTimer);
            if (!active) {
                if (document.title.startsWith('🔔')) document.title = this.baseTitle;
                return;
            }
            let on = false;
            this.titleTimer = setInterval(() => {
                if (!document.title.startsWith('🔔')) this.baseTitle = document.title;
                on = !on;
                document.title = on ? `🔔 (${this.waiting}) PANGGILAN!` : this.baseTitle;
            }, 1000);
        },
    }));

    // ---------- Grafik suhu & kelembapan ----------
    Alpine.data('roomChart', (roomId, tRange, hRange) => ({
        hours: 6, chart: null, timer: null,

        init() {
            const limit = (label, color, axis) => ({
                label, data: [], borderColor: color, borderDash: [6, 4],
                borderWidth: 1, pointRadius: 0, yAxisID: axis,
            });
            this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Suhu (°C)', data: [], borderColor: '#dc3545', backgroundColor: '#dc3545', yAxisID: 'y', tension: .3, pointRadius: 0 },
                        { label: 'Kelembapan (%)', data: [], borderColor: '#0d6efd', backgroundColor: '#0d6efd', yAxisID: 'y1', tension: .3, pointRadius: 0 },
                        limit('Batas suhu min', '#dc3545', 'y'),
                        limit('Batas suhu maks', '#dc3545', 'y'),
                        limit('Batas lembap min', '#0d6efd', 'y1'),
                        limit('Batas lembap maks', '#0d6efd', 'y1'),
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y:  { position: 'left',  title: { display: true, text: '°C' } },
                        y1: { position: 'right', title: { display: true, text: '%' }, grid: { drawOnChartArea: false } },
                    },
                    plugins: { legend: { labels: { filter: (item) => !item.text.startsWith('Batas') } } },
                },
            });
            this.load();
            this.timer = setInterval(() => this.load(), 15000);
        },
        destroy() {
            clearInterval(this.timer);
            this.chart?.destroy();
        },
        async load() {
            try {
                const res = await fetch(`/api/rooms/${roomId}/readings?hours=${this.hours}`, {
                    headers: { Accept: 'application/json' },
                });
                const rows = await res.json();
                const n = rows.length;
                const c = this.chart;
                c.data.labels = rows.map((r) =>
                    new Date(r.recorded_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                c.data.datasets[0].data = rows.map((r) => Number(r.temperature));
                c.data.datasets[1].data = rows.map((r) => Number(r.humidity));
                c.data.datasets[2].data = Array(n).fill(tRange[0]);
                c.data.datasets[3].data = Array(n).fill(tRange[1]);
                c.data.datasets[4].data = Array(n).fill(hRange[0]);
                c.data.datasets[5].data = Array(n).fill(hRange[1]);
                c.update('none');
            } catch (e) { /* abaikan, coba lagi di polling berikutnya */ }
        },
    }));
});