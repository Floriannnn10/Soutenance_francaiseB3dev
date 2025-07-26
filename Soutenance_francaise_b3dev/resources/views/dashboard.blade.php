@php
    // Redirection ou inclusion dynamique selon le rôle
    $user = Auth::user();
    $role = strtolower($user->roles->first()->nom ?? '');
@endphp
@if($role === 'admin')
    @include('dashboard.utilisateurs')
@else
    <!-- Ancien contenu ou message d'accès restreint -->
    <div class="p-8 text-center text-gray-500 text-xl">Accès réservé à l'administrateur.</div>
@endif

