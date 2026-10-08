<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NurseCall & CareRoom Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 font-sans text-slate-800" x-data="{ 
    isOnline: navigator.onLine, 
    audioAllowed: false,
    initAudio() {
        this.audioAllowed = true;
        const audio = new Audio('/alarm.mp3');
        audio.play().then(() => { audio.pause(); audio.currentTime = 0; }).catch(() => {});
    }
}" x-init="
    window.addEventListener('online', () => isOnline = true);
    window.addEventListener('offline', () => isOnline = false);
">

    <!-- Header / Navbar -->
    <header class="bg-blue-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-lg font-bold text-xl">🏥</div>
                <div>
                    <h1 class="text-lg font-bold leading-none">CareRoom & NurseCall</h1>
                    <span class="text-xs text-blue-200">System Monitoring Kamar Medis</span>
                </div>
                <!-- Indikator Koneksi Live -->
                <span :class="isOnline ? 'bg-emerald-500' : 'bg-rose-500'" class="ml-3 px-2.5 py-1 text-xs text-white rounded-full font-semibold shadow">
                    <span x-text="isOnline ? '● Online' : '● Offline'"></span>
                </span>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Tombol Audio Unlock -->
                <button 
                    x-show="!audioAllowed" 
                    @click="initAudio()" 
                    class="bg-amber-400 hover:bg-amber-500 text-slate-900 px-3 py-1.5 rounded-lg text-xs font-bold animate-bounce shadow">
                    🔔 Aktifkan Suara Alarm
                </button>
                <span x-show="audioAllowed" class="text-xs text-emerald-300 font-medium flex items-center gap-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Suara Alarm Aktif
                </span>

                <nav class="space-x-1 text-sm font-medium bg-blue-800/60 p-1 rounded-lg">
                    <a href="/" class="bg-blue-700 text-white px-3 py-1.5 rounded-md inline-block">Dashboard</a>
                    <a href="/scm" class="hover:bg-blue-700/50 text-blue-100 px-3 py-1.5 rounded-md inline-block">Stok SCM</a>
                    <a href="/laporan" class="hover:bg-blue-700/50 text-blue-100 px-3 py-1.5 rounded-md inline-block">Laporan</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>