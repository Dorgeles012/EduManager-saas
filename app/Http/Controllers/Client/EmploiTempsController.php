<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, Enseignant, Etablissement, Matiere};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EmploiTempsController extends Controller {
 public function index(Request $request) {
  $user=$request->user(); $classes=Classe::where('tenant_id',$user->tenant_id)->when($user->etablissement_id,fn($q)=>$q->where('etablissement_id',$user->etablissement_id))->orderBy('nom')->get();
  $classId=$request->integer('classe_id') ?: $classes->first()?->id;
  $entries=EmploiTemps::with(['matiere','enseignant','classe'])->where('tenant_id',$user->tenant_id)->when($classId,fn($q)=>$q->where('classe_id',$classId))->orderBy('heure_debut')->get();
  return view('client.emploi-temps', ['entries'=>$entries,'classes'=>$classes,'selectedClass'=>$classId,'years'=>AnneeAcademique::where('tenant_id',$user->tenant_id)->orderByDesc('date_debut')->get(),'subjects'=>Matiere::where('tenant_id',$user->tenant_id)->orderBy('nom')->get(),'establishments'=>Etablissement::where('tenant_id',$user->tenant_id)->get()]);
 }
 public function teachers(Request $request, Classe $classe) { abort_unless($classe->tenant_id===$request->user()->tenant_id,404); return response()->json($classe->enseignants()->orderBy('nom')->get(['enseignants.id','nom','prenoms'])); }
 public function store(Request $request) { $data=$this->validateEntry($request); $this->conflicts($data); EmploiTemps::create($data+['tenant_id'=>$request->user()->tenant_id]); return back()->with('success','Cours ajouté à l’emploi du temps.'); }
 public function destroy(Request $request, EmploiTemps $emploiTemps) { abort_unless($emploiTemps->tenant_id===$request->user()->tenant_id,404); $emploiTemps->delete(); return back()->with('success','Cours supprimé.'); }
 public function print(Request $request) { return $this->scheduleView($request, false); }
 public function pdf(Request $request) { $data=$this->scheduleData($request); return Pdf::loadView('client.emploi-temps-print',$data)->setPaper('a4','landscape')->download('emploi-du-temps.pdf'); }
 private function scheduleView(Request $request, bool $pdf) { return view('client.emploi-temps-print',$this->scheduleData($request)+['pdfMode'=>$pdf]); }
 private function scheduleData(Request $request): array { $user=$request->user(); $class=Classe::where('tenant_id',$user->tenant_id)->findOrFail($request->integer('classe_id')); return ['classe'=>$class,'entries'=>EmploiTemps::with(['matiere','enseignant'])->where('tenant_id',$user->tenant_id)->where('classe_id',$class->id)->orderBy('heure_debut')->get(),'school'=>Etablissement::find($user->etablissement_id),'year'=>AnneeAcademique::find($request->integer('annee_academique_id'))]; }
 private function validateEntry(Request $request): array { $user=$request->user(); return $request->validate(['etablissement_id'=>['required','integer'],'annee_academique_id'=>['required','integer'],'classe_id'=>['required','integer'],'jour'=>['required','in:lundi,mardi,mercredi,jeudi,vendredi,samedi'],'heure_debut'=>['required','date_format:H:i'],'heure_fin'=>['required','date_format:H:i','after:heure_debut'],'matiere_id'=>['required','integer'],'enseignant_id'=>['required','integer'],'salle'=>['nullable','string','max:100']])+[]; }
 private function conflicts(array $data): void { $overlap=fn($q)=>$q->where('jour',$data['jour'])->where('heure_debut','<',$data['heure_fin'])->where('heure_fin','>',$data['heure_debut']); $base=EmploiTemps::query()->where('tenant_id',auth()->user()->tenant_id); if((clone $base)->where('classe_id',$data['classe_id'])->where($overlap)->exists()) abort(422,'Conflit : cette classe a déjà un cours à cet horaire.'); if((clone $base)->where('enseignant_id',$data['enseignant_id'])->where($overlap)->exists()) abort(422,'Conflit : cet enseignant a déjà un cours à cet horaire.'); if(!empty($data['salle'])&&(clone $base)->where('salle',$data['salle'])->where($overlap)->exists()) abort(422,'Conflit : cette salle est déjà occupée à cet horaire.'); }
}
