<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'PorciGest') – PorciGest</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        .ok { background:#e6f4ea; border-left:4px solid #2e7d32; padding:.6rem 1rem; }
        .ko { background:#fdecea; border-left:4px solid #c62828; padding:.6rem 1rem; }
        .crit { color:#c62828; font-weight:bold; }
        .icon { display:inline-block; width:1em; height:1em; vertical-align:-.15em; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
        .brand-icon { width:1.35em; height:1.35em; vertical-align:-.3em; }
        .grid-form { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; align-items:end; }
        nav a { margin-right:.9rem; }
        small.muted { color:#777; }
        table { font-size:.9rem; }
        .pagination { display:flex; gap:.4rem; list-style:none; padding:0; flex-wrap:wrap; }
        .pagination .active span { font-weight:bold; }
    </style>
</head>
<body>
<nav class="container-fluid">
    <strong><svg class="icon brand-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5.5 9 4 5l5 2.1A9 9 0 0 1 12 6.5c1.1 0 2.1.2 3 .6L20 5l-1.5 5a7.5 7.5 0 0 1 1 3.8 6 6 0 0 1-6 6H10a6 6 0 0 1-6-6A7.5 7.5 0 0 1 5.5 9Z"/><ellipse cx="12" cy="14" rx="3" ry="2"/><path d="M10.8 14v.1m2.4-.1v.1M8.5 11h.01m7 0h.01"/></svg> PorciGest</strong>
    @auth
        <a href="{{ route('dashboard') }}">Tableau de bord</a>
        <a href="{{ route('unites.index') }}">Unités</a>
        <a href="{{ route('campagnes.index') }}">Campagnes</a>
        <a href="{{ route('intrants.index') }}">Intrants</a>
        <a href="{{ route('distributions.index') }}">Alimentation</a>
        <a href="{{ route('activites.index') }}">Activités</a>
        @can('voir-finances') <a href="{{ route('finance.index') }}">Finances</a> @endcan
        @can('gerer-utilisateurs') <a href="{{ route('users.index') }}">Utilisateurs</a> @endcan
        <a href="{{ route('notifications.index') }}"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12a2 2 0 0 0 4 0"/></svg> Notifications ({{ auth()->user()->unreadNotifications()->count() }})</a>
        <span style="float:right">
            {{ auth()->user()->name }} ({{ auth()->user()->role }})
            <form method="post" action="{{ route('logout') }}" style="display:inline">@csrf
                <button class="secondary outline" style="padding:.2rem .6rem">Déconnexion</button>
            </form>
        </span>
    @endauth
</nav>
<main class="container">
    @if (session('succes')) <p class="ok">{{ session('succes') }}</p> @endif
    @if ($errors->any())
        <div class="ko"><ul style="margin:0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif
    @yield('contenu')
</main>
</body>
</html>
