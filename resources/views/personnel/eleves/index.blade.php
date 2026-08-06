@extends('personnel.layouts.app')
@section('title','EduManager - Eleves')
@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Gestion des Élèves</h2>
        <p class="text-body-sm text-text-muted text-sm">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <button class="flex items-center gap-1.5 px-4 py-1.5 bg-primary text-white rounded-lg text-sm hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-standard')" type="button">
        <span class="material-symbols-outlined text-base">person_add</span>Nouvel élève
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-primary-fixed flex items-center justify-center text-primary"><span class="material-symbols-outlined text-2xl">school</span></div>
        <div><h3 class="text-xs text-text-muted">Élèves inscrits</h3><p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalStudents ?? 0 }}</p></div>
    </div>
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-secondary-container flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-2xl">meeting_room</span></div>
        <div><h3 class="text-xs text-text-muted">Classes actives</h3><p class="text-headline-lg font-headline-lg text-on-surface">{{ $activeClasses ?? 0 }}</p></div>
    </div>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-4 py-2.5 border-b border-surface-subtle bg-surface-container-low flex justify-between items-center">
        <h4 class="font-headline-sm text-headline-sm text-primary text-base">Liste des élèves</h4>
    </div>
    <form method="GET" action="{{ route('personnel.eleves.index') }}" class="px-4 py-3 border-b border-surface-subtle grid grid-cols-1 md:grid-cols-5 gap-2 bg-white">
        <input type="text" name="search" value="{{ request('search') }}" class="rounded-lg border-outline-variant text-sm px-3 py-1.5" placeholder="Rechercher nom ou matricule">
        <select name="niveau_id" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Tous les niveaux</option>
            @foreach($levels ?? [] as $level)<option value="{{ $level['id'] }}" @selected((string)request('niveau_id')===(string)$level['id'])>{{ $level['name'] }}</option>@endforeach
        </select>
        <select name="id_serie" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Toutes les séries</option>
            @foreach($series ?? [] as $serie)<option value="{{ $serie->id }}" @selected((string)request('id_serie')===(string)$serie->id)>{{ $serie->nom_serie }}</option>@endforeach
        </select>
        <select name="classe_id" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $classe)<option value="{{ $classe['id'] }}" @selected((string)request('classe_id')===(string)$classe['id'])>{{ $classe['name'] }}</option>@endforeach
        </select>
        <button class="bg-primary text-white rounded-lg px-4 py-1.5 text-sm" type="submit">Filtrer</button>
    </form>

    @if(($students ?? collect())->isEmpty())
    <div class="min-h-[160px] flex flex-col items-center justify-center text-center p-6">
        <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-3"><span class="material-symbols-outlined text-primary text-4xl">school</span></div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 text-base">Aucun élève enregistré</h3>
    </div>
    @else
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-sm border-separate border-spacing-y-1">
            <thead class="bg-surface-container-low text-xs uppercase tracking-wider text-text-muted">
                <tr><th class="px-2 py-2.5 font-semibold">#</th><th class="px-3 py-2.5 font-semibold min-w-[160px]">Nom &amp; Prénoms</th><th class="px-2 py-2.5 font-semibold">Matricule</th><th class="px-2 py-2.5 font-semibold">Sexe</th><th class="px-2 py-2.5 font-semibold">Classe</th><th class="px-2 py-2.5 font-semibold">Niveau</th><th class="px-2 py-2.5 font-semibold min-w-[80px]">Série</th><th class="px-2 py-2.5 font-semibold min-w-[90px]">Date naiss.</th><th class="px-2 py-2.5 font-semibold text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @foreach($students as $student)
                @php $sexe=strtolower(trim($student['sexe']??'')); $badge=in_array($sexe,['m','masculin','male','homme','h'])?'bg-blue-100 text-blue-700':(in_array($sexe,['f','féminin','feminin','female','femme','f'])?'bg-pink-100 text-pink-700':'bg-gray-100 text-gray-700'); $display=in_array($sexe,['m','masculin','male','homme','h'])?'Masculin':(in_array($sexe,['f','féminin','feminin','female','femme','f'])?'Féminin':'N/A'); @endphp
                <tr class="hover:bg-surface-container-low transition-colors rounded-lg shadow-sm bg-white">
                    <td class="px-2 py-2.5 align-middle">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2.5 min-w-[160px] align-middle"><span class="font-medium whitespace-nowrap text-sm">{{ $student['lastname'] }} {{ $student['firstname'] }}</span></td>
                    <td class="px-2 py-2.5 text-text-muted align-middle text-sm">{{ $student['matricule'] ?? 'N/A' }}</td>
                    <td class="px-2 py-2.5 align-middle"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $display }}</span></td>
                    <td class="px-2 py-2.5 text-text-muted align-middle text-sm">{{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</td>
                    <td class="px-2 py-2.5 align-middle"><span class="px-2 py-1 rounded-full text-xs font-medium bg-secondary-container/20 text-on-secondary-container">{{ $student['level'] }}</span></td>
                    <td class="px-2 py-2.5 text-text-muted align-middle min-w-[80px] text-sm">{{ $student['serie'] ?? '—' }}</td>
                    <td class="px-2 py-2.5 text-text-muted align-middle min-w-[90px] text-sm">{{ $student['birthdate'] }}</td>
                    <td class="px-2 py-2.5 text-right align-middle">
                        <div class="flex justify-end items-center gap-0.5">
                            <button class="inline-flex items-center justify-center p-1 text-primary hover:bg-primary-fixed rounded transition-colors leading-none" onclick="viewStudent({{ json_encode($student) }})" title="Voir" type="button"><span class="material-symbols-outlined text-base">visibility</span></button>
                            <button class="inline-flex items-center justify-center p-1 text-warning-amber hover:bg-warning-amber/10 rounded transition-colors leading-none" onclick="editStudent({{ json_encode($student) }})" title="Modifier" type="button"><span class="material-symbols-outlined text-base">edit</span></button>
                            <form action="{{ route('personnel.eleves.destroy', $student['id']) }}" method="POST" class="inline-flex items-center leading-none m-0 p-0 delete-student-form">@csrf @method('DELETE')<button class="inline-flex items-center justify-center p-1 text-alert-red hover:bg-error-container/20 rounded transition-colors delete-student-btn leading-none" data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}" title="Supprimer" type="button"><span class="material-symbols-outlined text-base">delete</span></button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-2.5 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between text-sm">
        <span class="text-text-muted text-xs">Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves</span>
        <div class="flex gap-1 text-sm">{{ $students->links() ?? '' }}</div>
    </div>
    @endif
</div>
@include('personnel.partials.student-modals')
@endsection

@push('styles')
<style>.material-symbols-outlined{font-variation-settings:'FILL'0,'wght'400,'GRAD'0,'opsz'24;font-size:20px}.glass-card{background:rgba(255,255,255,.8);backdrop-filter:blur(12px);border:1px solid rgba(226,232,240,1)}.modal-overlay{transition:backdrop-filter .3s ease}#modal-standard,#modal-view,#modal-edit{transition:opacity .3s ease}table th,table td{padding-top:0.5rem!important;padding-bottom:0.5rem!important}</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php $availableClassesForJs=collect($classes??[])->values(); $availableSeriesForJs=($series??collect())->map(fn($s)=>['id'=>$s->id,'class_ids'=>$s->classes->pluck('id')->values(),'nom_serie'=>$s->nom_serie])->values(); @endphp
<script>
const availableClasses=@json($availableClassesForJs),availableSeries=@json($availableSeriesForJs);
function populateClasses(l,c,s,w,sc='',ss=''){
    const id=document.getElementById(l)?.value,sel=document.getElementById(c);if(!sel)return;
    const m=availableClasses.filter(cls=>String(cls.level_id)===String(id));
    sel.innerHTML='<option value="">Sélectionner une classe</option>'+m.map(cls=>`<option value="${cls.id}">${cls.name}</option>`).join('');
    sel.disabled=!id||m.length===0;
    sel.value=m.some(cls=>String(cls.id)===String(sc))?String(sc):'';
    populateSeries(c,s,w,ss);
}
function populateSeries(c,s,w,id=''){
    const classId=document.getElementById(c)?.value,sel=document.getElementById(s),wr=document.getElementById(w);if(!sel||!wr)return;
    const m=availableSeries.filter(serie=>(serie.class_ids??[]).map(String).includes(String(classId)));
    sel.innerHTML=`<option value="">${m.length?'Sélectionner une série':'Aucune série pour cette classe'}</option>`+m.map(serie=>`<option value="${serie.id}">${serie.nom_serie}</option>`).join('');
    wr.classList.remove('hidden');sel.disabled=!classId||m.length===0;
    sel.value=m.some(serie=>String(serie.id)===String(id))?String(id):'';
}
function previewPhoto(i,p){const pr=document.getElementById(p);if(i.files&&i.files[0]){const r=new FileReader();r.onload=e=>{pr.innerHTML=`<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover object-center">`};r.readAsDataURL(i.files[0])}else pr.innerHTML='<span class="material-symbols-outlined text-2xl text-text-muted">photo_camera</span>';}
function openModal(id){const m=document.getElementById(id),c=document.getElementById(id+'-content');m.classList.remove('hidden');m.classList.add('flex');document.body.style.overflow='hidden';setTimeout(()=>{c.classList.remove('scale-95','opacity-0');c.classList.add('scale-100','opacity-100')},10);}
function closeModal(id){const m=document.getElementById(id),c=document.getElementById(id+'-content');c.classList.remove('scale-100','opacity-100');c.classList.add('scale-95','opacity-0');setTimeout(()=>{m.classList.remove('flex');m.classList.add('hidden');document.body.style.overflow='auto'},300);}
function viewStudent(s){openViewModal(s);}function editStudent(s){openEditModal(s);}
function openViewModal(s){
    document.getElementById('viewStudentFullName').textContent=(s.firstname??'')+' '+(s.lastname??'')||'-';
    ['matricule','birthdate','birthplace','classe','level','serie','nationalite','parent_lastname','parent_firstname','parent_phone','parent_email','created_at','updated_at'].forEach(f=>{
        const el=document.getElementById('viewStudent'+f.charAt(0).toUpperCase()+f.slice(1));
        if(el) el.textContent=s[f]??(f==='classe'?s.class??'N/A':'N/A');
        if(f==='classe'&&!s.classe&&s.class) document.getElementById('viewStudentClasse').textContent=s.class;
    });
    document.getElementById('viewStudentInterne').textContent=s.interne?'Oui':'Non';
    document.getElementById('viewStudentAffecte').textContent=s.affecte?'Oui':'Non';
    const sexe=(s.sexe??'').toString().trim().toLowerCase();
    document.getElementById('viewStudentSexeDetail').textContent=['m','masculin','male','homme','h'].includes(sexe)?'Masculin':['f','féminin','feminin','female','femme','f'].includes(sexe)?'Féminin':'Non renseigné';
    openModal('modal-view');
}
function openEditModal(s){
    document.getElementById('editEleveId').value=s.id;
    ['lastname','firstname','matricule','birthplace','parent_lastname','parent_firstname','parent_phone','parent_email'].forEach(f=>{
        const el=document.getElementById('edit'+f.replace('_','').charAt(0).toUpperCase()+f.replace('_','').slice(1));
        if(el) el.value=s[f]??'';
    });
    document.getElementById('editBirthdate').value=s.birthdate_raw??'';
    document.getElementById('editLevel').value=s.level_id??'';
    populateClasses('editLevel','editClasse','editSerie','editSerieWrapper',s.class_id??'',s.serie_id??'');
    document.getElementById('editInterne').value=s.interne?'1':'0';
    document.getElementById('editAffecte').value=s.affecte?'1':'0';
    document.getElementById('form-edit').action=`/personnel/eleves/${s.id}`;
    openModal('modal-edit');
}
document.addEventListener('DOMContentLoaded',function(){
    ['stdLevel','editLevel'].forEach(id=>document.getElementById(id)?.addEventListener('change',()=>populateClasses(id,'stdClasse','stdSerie','stdSerieWrapper')));
    ['stdClasse','editClasse'].forEach(id=>document.getElementById(id)?.addEventListener('change',()=>populateSeries(id,'stdSerie','stdSerieWrapper')));
    document.querySelectorAll('.delete-student-btn').forEach(b=>b.addEventListener('click',function(e){
        e.preventDefault();Swal.fire({title:'Êtes-vous sûr ?',text:`L'élève "${this.dataset.name}" sera définitivement supprimé.`,icon:'warning',showCancelButton:!0,confirmButtonColor:'#ba1a1a',cancelButtonColor:'#64748B',confirmButtonText:'Oui, supprimer',cancelButtonText:'Annuler'}).then(r=>{if(r.isConfirmed)this.closest('form').submit()});
    }));
    @if(session('success')) Swal.fire({icon:'success',title:'Succès',text:@json(session('success')),timer:2500,showConfirmButton:!1});@endif
    @if(session('error')) Swal.fire({icon:'error',title:'Erreur',text:@json(session('error')),timer:3000,showConfirmButton:!1});@endif
});
</script>
@endpush