<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, Enseignant, Etablissement, Matiere, Series};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmploiTempsController extends Controller
{
    private const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
    private const SLOTS = [['07:00','07:55'],['07:55','08:50'],['08:50','09:45'],['break'=>'Récréation'],['10:00','10:55'],['10:55','11:50'],['break'=>'Interclasse'],['14:00','15:00'],['15:00','16:00'],['16:00','17:00'],['17:00','18:00']];

    public function index(Request $request)
    {
        $user = $request->user();
        $classes = Classe::where('tenant_id', $user->tenant_id)->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))->orderBy('nom')->get();
        $classId = $request->integer('classe_id') ?: $classes->first()?->id;
        return view('client.emploi-temps', ['entries'=>EmploiTemps::with(['matiere','enseignant','classe'])->where('tenant_id',$user->tenant_id)->when($classId,fn($q)=>$q->where('classe_id',$classId))->orderBy('heure_debut')->get(),'classes'=>$classes,'selectedClass'=>$classId,'years'=>AnneeAcademique::where('tenant_id',$user->tenant_id)->orderByDesc('date_debut')->get(),'subjects'=>Matiere::where('tenant_id',$user->tenant_id)->orderBy('nom')->get(),'establishments'=>Etablissement::where('tenant_id',$user->tenant_id)->get()]);
    }

    public function create(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant, $request); abort_if($this->hasSchedule($enseignant, $request), 409, 'Cet enseignant possède déjà un emploi du temps.'); return $this->form($request, $enseignant, false); }
    public function edit(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant, $request); return $this->form($request, $enseignant, true); }
    public function show(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant, $request); return view('client.emploi-temps-show', $this->scheduleData($request, $enseignant)); }
    public function exists(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant, $request); return response()->json(['exists'=>$this->hasSchedule($enseignant, $request)]); }
    public function storeTeacherSchedule(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); if($this->hasSchedule($enseignant,$request)) throw ValidationException::withMessages(['schedule'=>'Cet enseignant possède déjà un emploi du temps. Utilisez la modification.']); $this->saveSchedule($request,$enseignant); return response()->json(['message'=>'Emploi du temps créé avec succès.','redirect'=>route('client.emploi-temps.show',$enseignant)]); }
    public function updateTeacherSchedule(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); $this->saveSchedule($request,$enseignant); return response()->json(['message'=>'Emploi du temps mis à jour avec succès.','redirect'=>route('client.emploi-temps.show',$enseignant)]); }
    public function printTeacher(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); return view('client.emploi-temps-print',$this->scheduleData($request,$enseignant)); }
    public function pdfTeacher(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); return Pdf::loadView('client.emploi-temps-print',$this->scheduleData($request,$enseignant))->setPaper('a4','landscape')->download('emploi-du-temps-'.$enseignant->id.'.pdf'); }

    // Historical class timetable endpoints remain available.
    public function teachers(Request $request, Classe $classe) { abort_unless((int)$classe->tenant_id === (int)$request->user()->tenant_id,404); return response()->json($classe->enseignants()->orderBy('nom')->get(['enseignants.id','nom','prenoms'])); }
    public function store(Request $request) { $data=$request->validate(['etablissement_id'=>['nullable','integer'],'annee_academique_id'=>['nullable','integer'],'classe_id'=>['required','integer'],'jour'=>['required','in:lundi,mardi,mercredi,jeudi,vendredi,samedi'],'heure_debut'=>['required','date_format:H:i'],'heure_fin'=>['required','date_format:H:i','after:heure_debut'],'matiere_id'=>['required','integer'],'enseignant_id'=>['required','integer'],'salle'=>['nullable','string','max:100']]); $this->validateExternalConflicts($request,$data,null); EmploiTemps::create($data+['tenant_id'=>$request->user()->tenant_id]); return back()->with('success','Cours ajouté à l’emploi du temps.'); }
    public function destroy(Request $request, EmploiTemps $emploiTemps) { abort_unless((int)$emploiTemps->tenant_id === (int)$request->user()->tenant_id,404); $emploiTemps->delete(); return back()->with('success','Cours supprimé.'); }
    public function print(Request $request) { return view('client.emploi-temps-class-print',$this->classScheduleData($request)); }
    public function pdf(Request $request) { return Pdf::loadView('client.emploi-temps-class-print',$this->classScheduleData($request))->setPaper('a4','landscape')->download('emploi-du-temps.pdf'); }

    private function form(Request $request, Enseignant $enseignant, bool $editing) { return view('client.emploi-temps-create',$this->scheduleData($request,$enseignant)+['editing'=>$editing]); }
    private function scheduleData(Request $request, Enseignant $enseignant): array
    {
        $user=$request->user(); $enseignant->loadMissing(['matiere','classes']);
        $entries=EmploiTemps::with(['classe','serie','matiere'])->where('tenant_id',$user->tenant_id)->where('enseignant_id',$enseignant->id)->get(); $grid=[];
        foreach($entries as $entry) $grid[$entry->jour][$this->time($entry->heure_debut).'-'.$this->time($entry->heure_fin)]=$entry;
        return ['enseignant'=>$enseignant,'classes'=>Classe::where('tenant_id',$user->tenant_id)->when($user->etablissement_id,fn($q)=>$q->where('etablissement_id',$user->etablissement_id))->orderBy('nom')->get(),'series'=>$enseignant->series()->orderBy('nom_serie')->get(),'subjects'=>$enseignant->matieres()->orderBy('nom')->get(),'years'=>AnneeAcademique::where('tenant_id',$user->tenant_id)->orderByDesc('date_debut')->get(),'entries'=>$entries,'grid'=>$grid,'days'=>self::DAYS,'slots'=>self::SLOTS,'establishmentId'=>$user->etablissement_id ?? $enseignant->etablissement_id,'school'=>Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id)];
    }
    private function saveSchedule(Request $request, Enseignant $enseignant): void
    {
        $data=$request->validate(['annee_academique_id'=>['nullable','integer'],'etablissement_id'=>['nullable','integer'],'cells'=>['nullable','array'],'cells.*.classe_id'=>['nullable','integer'],'cells.*.serie_id'=>['nullable','integer'],'cells.*.matiere_id'=>['nullable','integer'],'cells.*.salle'=>['nullable','string','max:100']]); $entries=[];$allowedSubjects=$enseignant->matieres()->pluck('id')->all();$allowedSeries=$enseignant->series()->pluck('id')->all();
        foreach($data['cells']??[] as $key=>$cell) { if(empty($cell['classe_id'])) continue; $parts=explode('|',$key); if(count($parts)!==3 || !in_array($parts[0],self::DAYS,true) || !$this->validSlot($parts[1],$parts[2])) throw ValidationException::withMessages(['cells'=>'Créneau invalide.']);$subject=(int)($cell['matiere_id']??0);$serie=empty($cell['serie_id'])?null:(int)$cell['serie_id'];if(!in_array($subject,$allowedSubjects,true))throw ValidationException::withMessages(['cells'=>'La matière choisie n’est pas attribuée à cet enseignant.']);if($serie&&!in_array($serie,$allowedSeries,true))throw ValidationException::withMessages(['cells'=>'La série choisie n’est pas attribuée à cet enseignant.']); $entries[]=['jour'=>$parts[0],'heure_debut'=>$parts[1],'heure_fin'=>$parts[2],'classe_id'=>(int)$cell['classe_id'],'serie_id'=>$serie,'matiere_id'=>$subject,'salle'=>trim($cell['salle']??'')?:null]; }
        $this->validateInternalConflicts($entries); foreach($entries as $entry) $this->validateExternalConflicts($request,$entry,$enseignant->id);
        DB::transaction(function() use($request,$enseignant,$entries,$data) { EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->delete(); foreach($entries as $entry) EmploiTemps::create($entry+['tenant_id'=>$request->user()->tenant_id,'enseignant_id'=>$enseignant->id,'etablissement_id'=>$data['etablissement_id']??$request->user()->etablissement_id??$enseignant->etablissement_id,'annee_academique_id'=>$data['annee_academique_id']??null]); });
    }
    private function validateInternalConflicts(array $entries): void { foreach($entries as $i=>$a) foreach($entries as $j=>$b) if($i<$j && $a['jour']===$b['jour'] && $this->overlaps($a,$b) && ($a['classe_id']===$b['classe_id'] || ($a['serie_id'] && $a['serie_id']===$b['serie_id']) || ($a['salle'] && $a['salle']===$b['salle']))) throw ValidationException::withMessages(['cells'=>'Conflit dans la grille : une classe, une série ou une salle est déjà occupée sur ce créneau.']); }
    private function validateExternalConflicts(Request $request, array $entry, ?int $excludeTeacher): void { $base=EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('jour',$entry['jour'])->where('heure_debut','<',$entry['heure_fin'])->where('heure_fin','>',$entry['heure_debut']); if($excludeTeacher) $base->where('enseignant_id','<>',$excludeTeacher); if((clone $base)->where('classe_id',$entry['classe_id'])->exists()) throw ValidationException::withMessages(['cells'=>'Conflit : cette classe est déjà occupée à ce créneau.']); if(!empty($entry['serie_id']) && (clone $base)->where('serie_id',$entry['serie_id'])->exists()) throw ValidationException::withMessages(['cells'=>'Conflit : cette série est déjà occupée à ce créneau.']); if(!empty($entry['enseignant_id']) && (clone $base)->where('enseignant_id',$entry['enseignant_id'])->exists()) throw ValidationException::withMessages(['cells'=>'Conflit : cet enseignant enseigne déjà à ce créneau.']); if(!empty($entry['salle']) && (clone $base)->where('salle',$entry['salle'])->exists()) throw ValidationException::withMessages(['cells'=>'Conflit : cette salle est déjà occupée à ce créneau.']); }
    private function classScheduleData(Request $request): array { $class=Classe::where('tenant_id',$request->user()->tenant_id)->findOrFail($request->integer('classe_id')); return ['classe'=>$class,'entries'=>EmploiTemps::with(['matiere','enseignant'])->where('tenant_id',$request->user()->tenant_id)->where('classe_id',$class->id)->get(),'school'=>Etablissement::find($request->user()->etablissement_id),'year'=>AnneeAcademique::find($request->integer('annee_academique_id'))]; }
    private function guardTeacher(Enseignant $enseignant, Request $request): void { abort_unless((int)$enseignant->tenant_id === (int)$request->user()->tenant_id,404); }
    private function hasSchedule(Enseignant $enseignant, Request $request): bool { return EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->exists(); }
    private function validSlot(string $start,string $end): bool { foreach(self::SLOTS as $slot) if(isset($slot[0]) && $slot[0]===$start && $slot[1]===$end) return true; return false; }
    private function overlaps(array $a,array $b): bool { return $a['heure_debut']<$b['heure_fin'] && $a['heure_fin']>$b['heure_debut']; }
    private function time($value): string { return substr((string)$value,0,5); }
}
