@extends('sadmin.layouts.app')

@section('content')
<!-- En-tête avec bouton retour -->
<div class="mb-6">
    <a href="{{ route('sadmin.parametres') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span class="text-[12px] font-medium">Retour aux paramètres</span>
    </a>
</div>

<!-- Detailed Account Management View -->
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Profile Sidebar -->
    <aside class="w-full lg:w-80 space-y-4">
        <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50 flex flex-col items-center text-center">
            <div class="relative mb-4">
                <div class="h-24 w-24 rounded-full overflow-hidden border-4 border-primary/20 shadow-md">
                    <img alt="User Avatar" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBroeuZog9qGMBKH4_biRoVzXCnj6ZRLBUdtv2F-PQs8DV5qIq8_PHt90j6DrWVLEMD7EZkZWimKNyTIZ8-BZXwEvZaZQ8AYjprIU0Jf7GZ8sfpgFxZBMG4LQBwJCZMf7wWIEtQMcLxlVZC64U2-9s9PEzg9HlI1WRnu1k_UhC19pRIzwEOPrjUpaFKC-_5I77rtb7IgKsfSo2oiEGrLKfVgTuKinhRjxDKwfs_iSNu9roQ8e_-PtA58w4OluECuyrYE5-g2u9ScUwb">
                </div>
                <button class="absolute bottom-0 right-0 p-1 bg-gradient-to-r from-primary to-primary-container text-on-primary rounded-full shadow-md">
                    <span class="material-symbols-outlined text-[14px]">edit</span>
                </button>
            </div>
            <h3 class="font-headline-lg text-[18px] text-primary">Priince</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gradient-to-r from-primary/10 to-primary-container/10 text-primary mt-1">SADMIN</span>
            <div class="w-full mt-6 space-y-1 text-left">
                <a class="flex items-center gap-3 px-3 py-2.5 bg-gradient-to-r from-primary/5 to-primary-container/5 text-primary font-semibold rounded-xl cursor-pointer" href="#">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    <span class="text-[12px] font-medium">Mon profil</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Profile Form Content -->
    <div class="flex-1 space-y-6">
        <!-- Informations Personnelles -->
        <section class="bg-surface-container-lowest rounded-2xl p-6 card-shadow border border-surface-subtle/50">
            <div class="flex items-center gap-3 mb-5 border-b border-outline-variant/30 pb-3">
                <div class="bg-gradient-to-br from-primary/10 to-primary-container/10 p-1.5 rounded-lg">
                    <span class="material-symbols-outlined text-primary text-[18px]">person</span>
                </div>
                <h3 class="font-headline-lg text-[16px] text-on-surface">Informations personnelles</h3>
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nom</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="text" value="Priince">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Prénom</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" placeholder="Prénom" type="text">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Adresse email</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="email" value="admin@institution.fr">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Téléphone</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="tel" value="+33 6 00 00 00 00">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nom d'utilisateur</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="text" value="priince_admin">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Rôle</label>
                    <input class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface-container-low text-on-surface-variant cursor-not-allowed text-[13px]" disabled type="text" value="SADMIN">
                </div>
                <div class="md:col-span-2 flex justify-end pt-3">
                    <button class="px-6 py-2 bg-gradient-to-r from-primary to-primary-container text-on-primary font-label-md text-[12px] rounded-lg" type="submit">Enregistrer les modifications</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Theme Toggle Simulation
    const themeToggle = document.getElementById('theme-toggle');
    const toggleCircle = document.getElementById('toggle-circle');
    let isDarkMode = false;

    if (themeToggle && toggleCircle) {
        themeToggle.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            if (isDarkMode) {
                themeToggle.classList.replace('bg-outline-variant/50', 'bg-primary');
                toggleCircle.style.transform = 'translateX(24px)';
                document.documentElement.classList.add('dark');
            } else {
                themeToggle.classList.replace('bg-primary', 'bg-outline-variant/50');
                toggleCircle.style.transform = 'translateX(0)';
                document.documentElement.classList.remove('dark');
            }
        });
    }

    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('.material-symbols-outlined');
    passwordToggles.forEach(toggle => {
        if (toggle.textContent === 'visibility') {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    this.textContent = 'visibility';
                }
            });
        }
    });
</script>
@endsection