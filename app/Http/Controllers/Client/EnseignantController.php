<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Classe, EmploiTemps, Enseignant, Etablissement, Matiere, Series};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class EnseignantController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        return view('client.enseignants.create', [
            'matieres' => $this->matieresFor($user),
            'classes' => $this->classesFor($user),
            'series' => $this->seriesFor($user),
        ]);
    }

    public function edit(Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);
        $user = auth()->user();

        $enseignant->load(['matieres','classes','series']);

        $matieres = $this->matieresFor($user);
        $classes = $this->classesFor($user);
        $series = $this->seriesFor($user);

        return view('client.enseignants.edit', [
            'enseignant' => $enseignant,
            'matieres' => $matieres,
            'classes' => $classes,
            'series' => $series,
        ]);
    }

    public function index()
    {
        $user=auth()->user();
        $enseignants=Enseignant::with(['matieres','classes','series'])->where('tenant_id',$user->tenant_id)->when($user->etablissement_id,fn($q)=>$q->where('etablissement_id',$user->etablissement_id))->latest()->paginate(10);
        $matieres=$this->matieresFor($user);
        $totalSchedules=EmploiTemps::where('tenant_id',$user->tenant_id)->when($user->etablissement_id,fn($q)=>$q->where('etablissement_id',$user->etablissement_id))->distinct('enseignant_id')->count('enseignant_id');
        return view('client.enseignant',['teachers'=>$enseignants->through(fn($teacher)=>$this->teacherPayload($teacher)),'subjects'=>$matieres->map(fn($m)=>['id'=>$m->id,'name'=>$m->nom]),'classes'=>$this->classesFor($user),'series'=>$this->seriesFor($user),'totalTeachers'=>$enseignants->total(),'totalSubjects'=>$matieres->count(),'totalSchedules'=>$totalSchedules,'avgPerSubject'=>$matieres->count()?round($enseignants->total()/$matieres->count(),1):0]);
    }
    public function store(Request $request) { $this->normaliseRelationIds($request); $validated=$this->validateEnseignant($request); $user=auth()->user(); $teacher=DB::transaction(function()use($validated,$user,$request){$teacher=Enseignant::create($this->attributes($validated,$user,$request));$teacher->matieres()->sync($validated['matiere_ids']);$teacher->classes()->sync($validated['classe_ids']);$teacher->series()->sync($validated['serie_ids']??[]);return $teacher->load(['matieres','classes','series']);}); return $this->response($request,$teacher,'Enseignant créé avec succès.'); }
    public function update(Request $request, Enseignant $enseignant) { $this->authorizeTenant($enseignant);$this->normaliseRelationIds($request);$validated=$this->validateEnseignant($request,$enseignant);$teacher=DB::transaction(function()use($validated,$enseignant,$request){$enseignant->update($this->attributes($validated,null,$request,$enseignant));$enseignant->matieres()->sync($validated['matiere_ids']);$enseignant->classes()->sync($validated['classe_ids']);$enseignant->series()->sync($validated['serie_ids']??[]);return $enseignant->fresh(['matieres','classes','series']);});return $this->response($request,$teacher,'Enseignant mis à jour avec succès.'); }
    public function destroy(Enseignant $enseignant) { $this->authorizeTenant($enseignant);$enseignant->matieres()->detach();$enseignant->classes()->detach();$enseignant->series()->detach();$enseignant->delete();return request()->expectsJson()?response()->json(['success'=>true,'message'=>'Enseignant supprimé avec succès.']):back()->with('success','Enseignant supprimé avec succès.'); }
    private function attributes(array $v, $user=null, ?Request $request=null, ?Enseignant $teacher=null): array { $first=(int)$v['matiere_ids'][0];$data=['nom'=>$v['nom'],'prenoms'=>$v['prenoms'],'email'=>$v['email'],'telephone'=>$v['telephone'],'matricule'=>$v['matricule'],'nombre_annees_enseignement'=>$v['nombre_annees_enseignement'],'sexe'=>$v['sexe'],'matiere_id'=>$first,'specialite'=>Matiere::find($first)?->nom];if($user){$data+=['tenant_id'=>$user->tenant_id,'etablissement_id'=>$user->etablissement_id??Etablissement::where('tenant_id',$user->tenant_id)->value('id'),'password'=>Hash::make('12345678'),'statut'=>'active'];}if($request?->hasFile('photo'))$data['photo']=$request->file('photo')->store('enseignants','public');return $data; }
    private function validateEnseignant(Request $request, ?Enseignant $teacher=null): array { $user=auth()->user();return $request->validate(['nom'=>['required','string','max:255'],'prenoms'=>['required','string','max:255'],'email'=>['required','email','max:255',Rule::unique('enseignants','email')->ignore($teacher?->id)],'telephone'=>['required','string','max:50'],'matricule'=>['required','string','max:100',Rule::unique('enseignants','matricule')->ignore($teacher?->id)],'nombre_annees_enseignement'=>['required','integer','min:0','max:80'],'sexe'=>['required','in:Masculin,Féminin'],'photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],'matiere_ids'=>['required','array','min:1','max:2'],'matiere_ids.*'=>[Rule::exists('matieres','id')->where(fn($q)=>$q->where('tenant_id',$user->tenant_id))],'classe_ids'=>['required','array','min:1'],'classe_ids.*'=>[Rule::exists('classes','id')->where(fn($q)=>$q->where('tenant_id',$user->tenant_id))],'serie_ids'=>['nullable','array'],'serie_ids.*'=>[Rule::exists('series','id')->where(fn($q)=>$q->where('tenant_id',$user->tenant_id))]]); }
    private function response(Request $request, Enseignant $teacher,string $message) { return $request->expectsJson()?response()->json(['success'=>true,'message'=>$message,'teacher'=>$this->teacherPayload($teacher)]):back()->with('success',$message); }
private function teacherPayload(Enseignant $t): array {
        return [
            'id' => $t->id,
            'firstname' => $t->prenoms ?? '',
            'lastname' => $t->nom,
            'email' => $t->email,
            'phone' => $t->telephone,
            'matricule' => $t->matricule,
            'teaching_years' => $t->nombre_annees_enseignement,
            'sexe' => $t->sexe,
            'photo' => $t->photo,
            'subject_ids' => $t->matieres->pluck('id')->all(),
            'subject' => ($t->matieres->pluck('nom')->join(', ') ?: 'Non assignée'),
            'status' => $t->statut,
            'class_ids' => $t->classes->pluck('id')->all(),
            'serie_ids' => $t->series->pluck('id')->all(),
        ];
    }
    private function authorizeTenant(Enseignant $teacher): void {$user=auth()->user();abort_unless((int)$teacher->tenant_id===(int)$user->tenant_id&&(!$user->etablissement_id||(int)$teacher->etablissement_id===(int)$user->etablissement_id),403);}
    private function normaliseRelationIds(Request $request): void { $values=[]; foreach(['matiere_ids','classe_ids','serie_ids'] as $field){$items=$request->input($field,[]);$items=is_array($items)?$items:[$items];$values[$field]=collect($items)->flatMap(fn($item)=>preg_split('/\s*,\s*/',(string)$item,-1,PREG_SPLIT_NO_EMPTY))->filter(fn($id)=>ctype_digit((string)$id))->map(fn($id)=>(int)$id)->unique()->values()->all();} $request->merge($values); }
    private function matieresFor($user) { return Matiere::query()->orderBy('nom')->get(['id','nom']); }
    private function classesFor($user) { $query=Classe::query(); if(Schema::hasColumn('classes','tenant_id')) $query->where('tenant_id',$user->tenant_id); if($user->etablissement_id && Schema::hasColumn('classes','etablissement_id')) $query->where('etablissement_id',$user->etablissement_id); return $query->orderBy('nom')->get(['id','nom']); }
    private function seriesFor($user) { $query=Series::query(); if(Schema::hasColumn('series','tenant_id')) $query->where('tenant_id',$user->tenant_id); if($user->etablissement_id && Schema::hasColumn('series','etablissement_id')) $query->where('etablissement_id',$user->etablissement_id); return $query->orderBy('nom_serie')->get(['id','nom_serie']); }
}
