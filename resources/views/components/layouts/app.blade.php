<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('rooms') }}" wire:navigate>NurseCall</a>

            <div class="navbar-nav me-auto">
                @foreach (['rooms' => 'Peta Kamar', 'queue' => 'Antrean', 'history' => 'Riwayat', 'stock' => 'Stok'] as $r => $label)
                    <a class="nav-link {{ request()->routeIs($r) ? 'active' : '' }}"
                       href="{{ route($r) }}" wire:navigate>{{ $label }}</a>
                @endforeach
            </div>

            <div class="d-flex align-items-center gap-2">
                <span x-data="connection" class="badge"
                      :class="online ? 'bg-success' : 'bg-danger'"
                      x-text="online ? 'Terhubung' : 'Terputus'"></span>

                @persist('alarm')
                    <livewire:alarm />
                @endpersist
            </div>
        </div>
    </nav>

    <main class="container-fluid pb-4">
        {{ $slot }}
    </main>

    @livewireScripts
    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
</body>
</html>