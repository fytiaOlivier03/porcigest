@extends('layouts.app')
@section('titre', 'Notifications')
@section('contenu')
<h1>Notifications</h1>
<form method="post" action="{{ route('notifications.lire') }}">@csrf <button class="outline">Tout marquer comme lu</button></form>
<table>
    @forelse ($notifications as $n)
        <tr><td>{{ $n->created_at->format('d/m/Y H:i') }}</td><td>{{ $n->read_at ? '' : '🆕' }}</td><td>{{ $n->data['message'] ?? '' }}</td></tr>
    @empty <tr><td>Aucune notification.</td></tr> @endforelse
</table>
{{ $notifications->links() }}
@endsection
