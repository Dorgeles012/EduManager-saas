<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-bold text-indigo-700">EduManager</a>
        @auth
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('profile.edit') }}">Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Déconnexion</button></form>
            </div>
        @endauth
    </div>
</nav>
