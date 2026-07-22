<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, Enseignant, Etablissement, Matiere, Series};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmploiTempsController extends Controller
{
    private const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
    private const SLOTS = [
        ['key' => 'slot-1', 'start' => '07:00', 'end' => '07:55'],
        ['key' => 'slot-2', 'start' => '07:55', 'end' => '08:50'],
        ['key' => 'slot-3', 'start' => '08:50', 'end' => '09:45'],
        ['break' => 'Récréation'],
        ['key' => 'slot-4', 'start' => '10:00', 'end' => '10:55'],
        ['key' => 'slot-5', 'start' => '10:55', 'end' => '11:50'],
        ['break' => 'Interclasse'],
        ['key' => 'slot-6', 'start' => '14:00', 'end' => '15:00'],
        ['key' => 'slot-7', 'start' => '15:00', 'end' => '16:00'],
        ['key' => 'slot-8', 'start' => '16:00', 'end' => '17:00'],
        ['key' => 'slot-9', 'start' => '17:00', 'end' => '18:00'],
    ];

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
    public function destroyTeacherSchedule(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); $deleted=DB::transaction(function() use($request,$enseignant) { DB::table('emploi_temps_slots')->where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->delete(); return EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->delete(); }); if(!$deleted) return response()->json(['message'=>'Aucun emploi du temps à supprimer.'],404); return response()->json(['message'=>'Emploi du temps supprimé avec succès.']); }
    public function printTeacher(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); return view('client.emploi-temps-print',$this->scheduleData($request,$enseignant)); }
    public function pdfTeacher(Request $request, Enseignant $enseignant) { $this->guardTeacher($enseignant,$request); return Pdf::loadView('client.emploi-temps-print',$this->scheduleData($request,$enseignant))->setPaper('a4','landscape')->download('emploi-du-temps-'.$enseignant->id.'.pdf'); }

    // Historical class timetable endpoints remain available.
    public function teachers(Request $request, Classe $classe) { abort_unless((int)$classe->tenant_id === (int)$request->user()->tenant_id,404); return response()->json($classe->enseignants()->orderBy('nom')->get(['enseignants.id','nom','prenoms'])); }
    public function store(Request $request) { $data=$request->validate(['etablissement_id'=>['nullable','integer'],'annee_academique_id'=>['nullable','integer'],'classe_id'=>['required','integer'],'jour'=>['required','in:lundi,mardi,mercredi,jeudi,vendredi,samedi'],'heure_debut'=>['required','date_format:H:i'],'heure_fin'=>['required','date_format:H:i','after:heure_debut'],'matiere_id'=>['required','integer'],'enseignant_id'=>['required','integer'],'salle'=>['nullable','string','max:100']]); $data=$this->normaliseEntry($data, $request); $this->validateExternalConflicts($request,$data,null); EmploiTemps::create($data+['tenant_id'=>$request->user()->tenant_id]); return back()->with('success','Cours ajouté à l’emploi du temps.'); }
    public function destroy(Request $request, EmploiTemps $emploiTemps) { abort_unless((int)$emploiTemps->tenant_id === (int)$request->user()->tenant_id,404); $emploiTemps->delete(); return back()->with('success','Cours supprimé.'); }
    public function print(Request $request) { return view('client.emploi-temps-class-print',$this->classScheduleData($request)); }
    public function pdf(Request $request) { return Pdf::loadView('client.emploi-temps-class-print',$this->classScheduleData($request))->setPaper('a4','landscape')->download('emploi-du-temps.pdf'); }

    private function form(Request $request, Enseignant $enseignant, bool $editing) { return view('client.emploi-temps-create',$this->scheduleData($request,$enseignant)+['editing'=>$editing]); }
    private function scheduleData(Request $request, Enseignant $enseignant): array
    {
        $user=$request->user(); $enseignant->loadMissing(['matiere','classes']);
        $entries=EmploiTemps::with(['classe','serie','matiere'])->where('tenant_id',$user->tenant_id)->where('enseignant_id',$enseignant->id)->get();
        $savedSlots=DB::table('emploi_temps_slots')->where('tenant_id',$user->tenant_id)->where('enseignant_id',$enseignant->id)->get()->keyBy('slot_key');
        $slots=$this->slotsFor($entries, $savedSlots);
        $grid=[];
        foreach($entries as $entry) $grid[$entry->jour][$this->slotKeyFor($entry)]=$entry;
        $years = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')->get();
        $yearId = $entries->pluck('annee_academique_id')->filter()->first();
        return ['enseignant'=>$enseignant,'classes'=>Classe::where('tenant_id',$user->tenant_id)->when($user->etablissement_id,fn($q)=>$q->where('etablissement_id',$user->etablissement_id))->orderBy('nom')->get(),'series'=>$enseignant->series()->orderBy('nom_serie')->get(),'subjects'=>$enseignant->matieres()->orderBy('nom')->get(),'years'=>$years,'year'=>$yearId ? $years->firstWhere('id', $yearId) : null,'entries'=>$entries,'grid'=>$grid,'days'=>self::DAYS,'slots'=>$slots,'establishmentId'=>$user->etablissement_id ?? $enseignant->etablissement_id,'school'=>Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id),'totalSeances'=>$entries->count()];
    }
    private function saveSchedule(Request $request, Enseignant $enseignant): void
    {
        $user = $request->user();
        $data=$request->validate(['annee_academique_id'=>['nullable','integer','exists:annee_academique,id'],'etablissement_id'=>['nullable','integer','exists:etablissements,id'],'slots'=>['required','array'],'slots.*.heure_debut'=>['required','date_format:H:i'],'slots.*.heure_fin'=>['required','date_format:H:i','after:slots.*.heure_debut'],'cells'=>['nullable','array'],'cells.*.classe_id'=>['nullable','integer','exists:classes,id'],'cells.*.serie_id'=>['nullable','integer','exists:series,id'],'cells.*.matiere_id'=>['nullable','integer','exists:matieres,id'],'cells.*.salle'=>['nullable','string','max:100']]);
        $establishmentId = (int) ($data['etablissement_id'] ?? $user->etablissement_id ?? $enseignant->etablissement_id);
        if ($establishmentId && (int) $user->etablissement_id !== 0 && (int) $user->etablissement_id !== $establishmentId) {
            throw ValidationException::withMessages(['etablissement_id' => 'Établissement non autorisé.']);
        }
        if (! empty($data['annee_academique_id']) && ! AnneeAcademique::where('tenant_id', $user->tenant_id)->where('id', $data['annee_academique_id'])->when($establishmentId, fn ($q) => $q->where('etablissement_id', $establishmentId))->exists()) {
            throw ValidationException::withMessages(['annee_academique_id' => 'Année académique non autorisée.']);
        }

        $entries=[];
        // belongsToMany joins the related table with its pivot table; both have an id column.
        $allowedSubjects=$enseignant->matieres()->pluck('matieres.id')->all();
        $allowedSeries=$enseignant->series()->pluck('series.id')->all();
        $slotDefinitions=$this->validatedSlots($data['slots']);
        foreach($data['cells']??[] as $key=>$cell) { if(empty($cell['classe_id'])) continue; $parts=explode('|',$key); if(count($parts)!==2 || !in_array($parts[0],self::DAYS,true) || !isset($slotDefinitions[$parts[1]])) throw ValidationException::withMessages(['cells'=>'Créneau invalide.']);$subject=(int)($cell['matiere_id']??0);$serie=empty($cell['serie_id'])?null:(int)$cell['serie_id'];if(!in_array($subject,$allowedSubjects,true))throw ValidationException::withMessages(['cells'=>'La matière choisie n’est pas attribuée à cet enseignant.']);if($serie&&!in_array($serie,$allowedSeries,true))throw ValidationException::withMessages(['cells'=>'La série choisie n’est pas attribuée à cet enseignant.']); if (!Classe::where('tenant_id',$user->tenant_id)->where('id',(int)$cell['classe_id'])->where('etablissement_id',$establishmentId)->exists()) throw ValidationException::withMessages(['cells'=>'Classe non autorisée.']); $slot=$slotDefinitions[$parts[1]]; $entries[]=$this->normaliseEntry(['jour'=>$parts[0],'heure_debut'=>$slot['heure_debut'],'heure_fin'=>$slot['heure_fin'],'slot_key'=>$parts[1],'classe_id'=>(int)$cell['classe_id'],'serie_id'=>$serie,'matiere_id'=>$subject,'enseignant_id'=>$enseignant->id,'salle'=>trim($cell['salle']??'')?:null,'etablissement_id'=>$establishmentId ?: null,'annee_academique_id'=>$data['annee_academique_id']??null], $request); }
        $this->validateInternalConflicts($entries); foreach($entries as $entry) $this->validateExternalConflicts($request,$entry,$enseignant->id);
        DB::transaction(function() use($request,$enseignant,$entries,$slotDefinitions) { EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->delete(); DB::table('emploi_temps_slots')->where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->delete(); DB::table('emploi_temps_slots')->insert(array_map(fn($slot,$key)=>['tenant_id'=>$request->user()->tenant_id,'enseignant_id'=>$enseignant->id,'slot_key'=>$key,'heure_debut'=>$this->timeAt($slot['heure_debut'])->format('H:i:s'),'heure_fin'=>$this->timeAt($slot['heure_fin'])->format('H:i:s'),'created_at'=>now(),'updated_at'=>now()],$slotDefinitions,array_keys($slotDefinitions))); foreach($entries as $entry) EmploiTemps::create($entry+['tenant_id'=>$request->user()->tenant_id]); });
    }
    private function validateInternalConflicts(array $entries): void { foreach($entries as $i=>$a) foreach($entries as $j=>$b) { if($i >= $j || $a['jour'] !== $b['jour'] || !$this->overlaps($a,$b)) continue; $sameSeries = $a['serie_id'] === $b['serie_id']; if($a['classe_id'] === $b['classe_id'] && $sameSeries) throw ValidationException::withMessages(['cells'=>'Cette classe et cette série sont déjà occupées sur ce créneau.']); if($a['enseignant_id'] === $b['enseignant_id']) throw ValidationException::withMessages(['cells'=>'Cet enseignant enseigne déjà sur ce créneau.']); if($a['serie_id'] && $sameSeries) throw ValidationException::withMessages(['cells'=>'Cette série est déjà occupée sur ce créneau.']); if($a['salle'] && $a['salle'] === $b['salle']) throw ValidationException::withMessages(['cells'=>'Cette salle est déjà occupée sur ce créneau.']); } }
    private function validateExternalConflicts(Request $request, array $entry, ?int $replacedTeacherId): void
    {
        $base=EmploiTemps::query()
            ->where('emploi_temps.tenant_id',$request->user()->tenant_id)
            ->where('emploi_temps.jour',$entry['jour'])
            ->where('emploi_temps.heure_debut','<',$entry['heure_fin'])
            ->where('emploi_temps.heure_fin','>',$entry['heure_debut']);

        foreach(['etablissement_id','annee_academique_id'] as $column) {
            $entry[$column] === null ? $base->whereNull('emploi_temps.'.$column) : $base->where('emploi_temps.'.$column,$entry[$column]);
        }

        // Teacher-schedule updates replace every row belonging to that teacher.
        // Those rows must not conflict with their own replacement rows.
        if($replacedTeacherId) $base->where('emploi_temps.enseignant_id','<>',$replacedTeacherId);

        $classConflict = (clone $base)->where('emploi_temps.classe_id', $entry['classe_id']);
        $entry['serie_id'] === null ? $classConflict->whereNull('emploi_temps.serie_id') : $classConflict->where('emploi_temps.serie_id', $entry['serie_id']);
        if($classConflict->exists()) throw ValidationException::withMessages(['cells'=>'Cette classe et cette série sont déjà occupées sur ce créneau.']);
        if((clone $base)->where('emploi_temps.enseignant_id',$entry['enseignant_id'])->exists()) throw ValidationException::withMessages(['cells'=>'Cet enseignant enseigne déjà sur ce créneau.']);
        if($entry['serie_id'] && (clone $base)->where('emploi_temps.serie_id',$entry['serie_id'])->exists()) throw ValidationException::withMessages(['cells'=>'Cette série est déjà occupée sur ce créneau.']);
        if($entry['salle'] && (clone $base)->where('emploi_temps.salle',$entry['salle'])->exists()) throw ValidationException::withMessages(['cells'=>'Cette salle est déjà occupée sur ce créneau.']);
    }
    private function classScheduleData(Request $request): array { $user=$request->user(); $class=Classe::where('tenant_id',$user->tenant_id)->when($user->etablissement_id, fn($q) => $q->where('etablissement_id',$user->etablissement_id))->findOrFail($request->integer('classe_id')); $yearId=$request->integer('annee_academique_id'); $entries=EmploiTemps::with(['matiere','enseignant','serie'])->where('tenant_id',$user->tenant_id)->where('classe_id',$class->id)->when($yearId,fn($q)=>$q->where('annee_academique_id',$yearId))->get(); $savedSlots=DB::table('emploi_temps_slots')->where('tenant_id',$user->tenant_id)->whereIn('enseignant_id',$entries->pluck('enseignant_id')->unique())->get()->groupBy('slot_key')->map->first(); $slots=$this->slotsFor($entries,$savedSlots); $grid=[]; foreach($entries as $entry) $grid[$entry->jour][$this->slotKeyFor($entry)]=$entry; return ['classe'=>$class,'entries'=>$entries,'grid'=>$grid,'days'=>self::DAYS,'slots'=>$slots,'school'=>Etablissement::find($class->etablissement_id),'year'=>$yearId ? AnneeAcademique::where('tenant_id',$user->tenant_id)->find($yearId) : null,'totalSeances'=>$entries->count()]; }
    private function guardTeacher(Enseignant $enseignant, Request $request): void { abort_unless((int)$enseignant->tenant_id === (int)$request->user()->tenant_id,404); }
    private function hasSchedule(Enseignant $enseignant, Request $request): bool { return EmploiTemps::where('tenant_id',$request->user()->tenant_id)->where('enseignant_id',$enseignant->id)->exists(); }
    private function validatedSlots(array $submittedSlots): array
    {
        $definitions=[];
        foreach(self::SLOTS as $slot) {
            if(isset($slot['break'])) continue;
            $value=$submittedSlots[$slot['key']]??null;
            if(!is_array($value)) throw ValidationException::withMessages(['slots'=>'Un créneau horaire est manquant.']);
            if(!$this->timeAt($value['heure_debut'])->lt($this->timeAt($value['heure_fin']))) throw ValidationException::withMessages(['slots'=>'L’heure de fin doit être postérieure à l’heure de début.']);
            $definitions[$slot['key']]=['heure_debut'=>$value['heure_debut'],'heure_fin'=>$value['heure_fin']];
        }

        foreach($definitions as $key=>$slot) foreach($definitions as $otherKey=>$other) {
            if($key >= $otherKey || !$this->overlaps($slot,$other)) continue;
            throw ValidationException::withMessages(['slots'=>'Les créneaux horaires ne doivent pas se chevaucher.']);
        }

        return $definitions;
    }
    private function slotsFor($entries, $savedSlots): array
    {
        $savedTimes=[];
        foreach($entries as $entry) $savedTimes[$this->slotKeyFor($entry)]??=['start'=>$this->time($entry->heure_debut),'end'=>$this->time($entry->heure_fin)];
        return array_map(function(array $slot) use($savedTimes,$savedSlots) {
            if(isset($slot['break'])) return $slot;
            $stored=$savedSlots->get($slot['key']);
            $times=$stored ? ['start'=>$this->time($stored->heure_debut),'end'=>$this->time($stored->heure_fin)] : ($savedTimes[$slot['key']]??[]);
            $slot=array_replace($slot, $times);
            return $slot + ['color'=>$this->colorForSlot($slot['start'])];
        }, self::SLOTS);
    }
    private function slotKeyFor(EmploiTemps $entry): string
    {
        if($entry->slot_key) return $entry->slot_key;
        foreach(self::SLOTS as $slot) if(!isset($slot['break']) && $slot['start']===$this->time($entry->heure_debut) && $slot['end']===$this->time($entry->heure_fin)) return $slot['key'];
        return 'legacy-'.$entry->id;
    }
    private function colorForSlot(string $start): string
    {
        $minutes=(int)substr($start,0,2) * 60 + (int)substr($start,3,2);
        return match (true) {
            $minutes < 8 * 60 + 30 => 'slot-color-1',
            $minutes < 9 * 60 + 30 => 'slot-color-2',
            $minutes < 10 * 60 + 45 => 'slot-color-3',
            $minutes < 11 * 60 + 45 => 'slot-color-4',
            $minutes < 12 * 60 + 45 => 'slot-color-5',
            $minutes < 15 * 60 => 'slot-color-6',
            $minutes < 16 * 60 => 'slot-color-7',
            default => 'slot-color-8',
        };
    }
    private function overlaps(array $a,array $b): bool { return $this->timeAt($a['heure_debut'])->lt($this->timeAt($b['heure_fin'])) && $this->timeAt($a['heure_fin'])->gt($this->timeAt($b['heure_debut'])); }
    private function normaliseEntry(array $entry, Request $request): array { $entry['heure_debut']=$this->timeAt($entry['heure_debut'])->format('H:i:s'); $entry['heure_fin']=$this->timeAt($entry['heure_fin'])->format('H:i:s'); $entry['serie_id']=$entry['serie_id']??null; $entry['salle']=filled($entry['salle']??null)?trim($entry['salle']):null; $entry['etablissement_id']=$entry['etablissement_id']??$request->user()->etablissement_id; $entry['annee_academique_id']=$entry['annee_academique_id']??null; return $entry; }
    private function timeAt(string $time): CarbonImmutable { $time=trim($time); return CarbonImmutable::createFromFormat('!H:i:s', strlen($time) === 5 ? $time.':00' : $time); }
    private function time($value): string { return substr((string)$value,0,5); }
}
