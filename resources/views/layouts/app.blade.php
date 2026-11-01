<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lapor MBG')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('reports.index') }}">Lapor MBG</a>
        @auth
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a class="nav-link text-white" href="{{ route('reports.index') }}">Laporan</a>
                @can('create-report')
                    <a class="nav-link text-white" href="{{ route('reports.create') }}">Buat Laporan</a>
                @endcan
                @can('view-recap')
                    <a class="nav-link text-white" href="{{ route('recap') }}">Rekap</a>
                @endcan
                @can('manage-master-data')
                    <a class="nav-link text-white" href="{{ route('schools.index') }}">Sekolah</a>
                    <a class="nav-link text-white" href="{{ route('distributions.index') }}">Distribusi</a>
                @endcan
                <span class="text-secondary small">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>

<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>