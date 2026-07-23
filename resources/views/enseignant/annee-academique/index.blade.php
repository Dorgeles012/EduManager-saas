@extends('enseignant.layouts.app')
@section('title', 'EduManager - Année académique')
@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Année Académique</h2>
        <p class="font-body-md text-text-muted mt-1">Consultation de l'année académique en cours</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter-desktop mb-10">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Année active</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $activeYear ?? '—' }}</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">update</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Statut</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">Active</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5 opacity-50">
        <div class="w-12 h-12 rounded-full bg-surface-subtle flex items-center justify-center">
            <span class="material-symbols-outlined text-outline">lock</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Modification</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">Non autorisée</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5 opacity-50">
        <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-outline">info</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Consultation seule</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">Lecture seule</h3>
        </div>
    </div>
</div>

<!-- Message d'information -->
<div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8 flex items-start gap-4">
    <div class="p-2 bg-amber-100 rounded-lg flex-shrink-0">
        <span class="material-symbols-outlined text-amber-600">info</span>
    </div>
    <div>
        <h4 class="font-headline-md text-headline-md text-amber-800 mb-1">Accès en consultation uniquement</h4>
        <p class="text-text-muted text-sm">Cette rubrique est accessible uniquement en consultation. Vous ne pouvez pas modifier les années académiques. Seul le personnel administratif peut effectuer des modifications.</p>
    </div>
</div>

<!-- Tableau principal -->
<div class="bg-surface-container-lowest rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 overflow-hidden">
    <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-on-surface">Année Académique Active</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-subtle/50">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Libellé de l'année</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Date début</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Date fin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @forelse($academicYears ?? collect() as $year)
                    <tr class="hover:bg-surface-subtle/20 transition-colors {{ $year->statut === 'active' ? 'bg-green-50/50' : 'opacity-70' }}" id="row-{{ $year->id }}">
                        <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>
                                </div>
                                <span class="font-label-md text-label-md text-on-surface">{{ $year->libelle }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php $isActive = $year->statut === 'active'; @endphp
                            <span class="px-3 py-1 {{ $isActive ? 'bg-green-500/20 text-green-700' : 'bg-gray-500/20 text-gray-600' }} text-[12px] font-bold rounded-full">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-on-surface-variant">{{ $year->date_debut ? \Carbon\Carbon::parse($year->date_debut)->format('d/m/Y') : '—' }}</td>
                        <td class="px-6 py-4 text-on-surface-variant">{{ $year->date_fin ? \Carbon\Carbon::parse($year->date_fin)->format('d/m/Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-48 h-48 bg-surface-subtle rounded-full flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-[80px] text-outline-variant">calendar_add_on</span>
                                </div>
                                <h5 class="font-headline-md text-headline-md text-on-surface">Aucune donnée trouvée</h5>
                                <p class="text-text-muted mt-2 max-w-sm">Aucune année académique n'est configurée pour le moment.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Succès', text: @json(session('success')), timer: 3000, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({ title: 'Erreur de validation', html: `<div class="text-left max-h-60 overflow-y-auto"><ul class="list-disc pl-5 text-red-600">@foreach($errors->all() as $error)<li class="mb-1">{{ $error }}</li>@endforeach</ul></div>`, icon: 'error', confirmButtonColor: '#4f46e5', confirmButtonText: 'Fermer' });
    @endif
</script>
@endsection

