<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Bulletin, Classe, Eleve, Enseignant, Etablissement};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EnseignantBulletinController extends Controller
{
    /**
     * Récupère l'enseignant connecté
     */
    private function getEnseignant(): ?Enseignant
    {
        $user = auth()->user();
        return Enseignant::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->with(['classes', 'matieres'])
            ->first();
    }

    /**
     * Affiche la liste des bulletins (consultation seulement)
     */
    public function index(Request $request): View
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return view('enseignant.bulletin.index', [
                'reportCards' => collect(),
                'classes' => collect(),
                'totalStudents' => 0,
                'totalClasses' => 0,
                'totalPeriods' => 0,
            ]);
        }

        $assignedClassIds = $enseignant->classes->pluck('id');

        $query = Bulletin::where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds)
            ->with(['eleve', 'classe', 'anneeAcademique']);

        $query->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->integer('classe_id')))
            ->when($request->filled('trimestre'), fn ($q) => $q->where('trimestre', $request->string('trimestre')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = (string) $request->string('q');
                $q->whereHas('eleve', fn ($student) => $student->where('nom', 'like', "%{$term}%")
                    ->orWhere('prenom', 'like', "%{$term}%")->orWhere('matricule', 'like', "%{$term}%"));
            });

        $reportCards = $query->latest()->paginate(15)->withQueryString()->through(fn (Bulletin $item) => [
            'id' => $item->id,
            'student_name' => trim($item->eleve?->nom.' '.$item->eleve?->prenom),
            'matricule' => $item->eleve?->matricule,
            'class_name' => $item->classe?->nom,
            'period' => strtoupper($item->trimestre),
            'average' => $item->moyenne_generale,
            'mention' => $item->mention ?? 'Non renseignée',
            'rank' => $item->rang,
        ]);

        $classes = Classe::whereIn('id', $assignedClassIds)
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get();

        $totalStudents = Eleve::where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds)
            ->count();

        return view('enseignant.bulletin.index', [
            'reportCards' => $reportCards,
            'classes' => $classes->map(fn ($c) => ['id' => $c->id, 'name' => $c->nom]),
            'totalStudents' => $totalStudents,
            'totalClasses' => $classes->count(),
            'totalPeriods' => Bulletin::where('tenant_id', $tenantId)
                ->whereIn('classe_id', $assignedClassIds)
                ->distinct()
                ->count('trimestre'),
        ]);
    }

    /**
     * Affiche un bulletin spécifique
     */
    public function show(Bulletin $bulletin)
    {
        $this->ensureAccess($bulletin);
        return $this->renderBulletin($bulletin);
    }

    /**
     * Impression d'un bulletin
     */
    public function print(Bulletin $bulletin)
    {
        $this->ensureAccess($bulletin);
        return $this->renderBulletin($bulletin, true);
    }

    /**
     * Téléchargement PDF d'un bulletin
     */
    public function downloadPdf(Bulletin $bulletin)
    {
        $this->ensureAccess($bulletin);
        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        $annualBulletins = Bulletin::query()
            ->where('tenant_id', $bulletin->tenant_id)
            ->where('eleve_id', $bulletin->eleve_id)
            ->where('annee_academique_id', $bulletin->annee_academique_id)
            ->whereIn('trimestre', ['t1', 't2', 't3'])
            ->with('disciplines')->get()->keyBy('trimestre');

        if ($annualBulletins->isEmpty()) {
            $annualBulletins = collect([$bulletin->trimestre => $bulletin]);
        }

        return Pdf::loadView('enseignant.bulletin.show', [
            'bulletin' => $bulletin,
            'annualBulletins' => $annualBulletins,
            'printMode' => false,
            'pdfMode' => true,
            'pdfSchoolLogo' => $this->pdfImageData($bulletin->etablissement?->logo, public_path('vendor/adminlte/dist/img/AdminLTELogo.png')),
            'pdfStudentPhoto' => $this->pdfImageData($bulletin->eleve?->photo, public_path('vendor/adminlte/dist/img/AdminLTELogo.png')),
        ])->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Times-Roman')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->download("bulletin-{$bulletin->id}.pdf");
    }

    /**
     * Téléchargement d'un bulletin (format texte)
     */
    public function download(Bulletin $bulletin)
    {
        $this->ensureAccess($bulletin);
        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'disciplines']);

        $lines = [
            'BULLETIN SCOLAIRE',
            'Élève : '.trim((string) ($bulletin->eleve?->nom.' '.$bulletin->eleve?->prenom)),
            'Matricule : '.($bulletin->eleve?->matricule ?? '-'),
            'Classe : '.($bulletin->classe?->nom ?? '-'),
            'Année académique : '.($bulletin->anneeAcademique?->libelle ?? '-'),
            'Période : '.strtoupper((string) $bulletin->trimestre),
            'Moyenne générale : '.number_format((float) $bulletin->moyenne_generale, 2, ',', ' ').'/20',
            'Mention : '.($bulletin->mention ?? '-'),
            'Date : '.($bulletin->date?->format('d/m/Y') ?? Carbon::today()->format('d/m/Y')),
        ];

        return response($this->makePdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bulletin-'.$bulletin->id.'.pdf"',
        ]);
    }

    /**
     * Vérifie que l'enseignant a accès au bulletin (même tenant + classe assignée)
     */
    private function ensureAccess(Bulletin $bulletin): void
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        abort_unless((int) $bulletin->tenant_id === (int) $tenantId, 404);

        if ($enseignant) {
            $assignedClassIds = $enseignant->classes->pluck('id')->toArray();
            abort_unless(in_array((int) $bulletin->classe_id, $assignedClassIds), 403, 'Vous n\'avez pas accès à ce bulletin.');
        }
    }

    private function renderBulletin(Bulletin $bulletin, bool $printMode = false)
    {
        $this->ensureAccess($bulletin);
        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        $annualBulletins = Bulletin::query()
            ->where('tenant_id', $bulletin->tenant_id)
            ->where('eleve_id', $bulletin->eleve_id)
            ->where('annee_academique_id', $bulletin->annee_academique_id)
            ->whereIn('trimestre', ['t1', 't2', 't3'])
            ->with('disciplines')
            ->get()
            ->keyBy('trimestre');

        if ($annualBulletins->isEmpty()) {
            $annualBulletins = collect([$bulletin->trimestre => $bulletin]);
        }

        return view('enseignant.bulletin.show', compact('bulletin', 'printMode', 'annualBulletins'));
    }

    private function pdfImageData(?string $path, ?string $fallback = null): ?string
    {
        $normalized = ltrim((string) $path, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $candidates = array_filter([
            $normalized !== '' ? storage_path('app/public/'.$normalized) : null,
            $normalized !== '' ? public_path($normalized) : null,
            $fallback,
        ]);

        foreach ($candidates as $file) {
            if (is_file($file) && is_readable($file)) {
                $mime = mime_content_type($file) ?: 'image/png';
                return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($file));
            }
        }

        return null;
    }

    private function makePdf(array $lines): string
    {
        $content = "BT\n/F1 10 Tf\n50 800 Td\n14 TL\n";
        foreach (array_slice($lines, 0, 52) as $line) {
            $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT', (string) $line) ?: '';
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);
            $content .= "({$escaped}) Tj\nT*\n";
        }
        $content .= "ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            "<< /Length ".strlen($content)." >>\nstream\n{$content}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf."trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}

