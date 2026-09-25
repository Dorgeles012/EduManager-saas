@php
    $recu = $recu ?? [];
    $etablissement = $etablissement ?? null;
@endphp
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reçu de paiement — {{ $recu['reference'] ?? 'N/R' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 24px;
            background: #f9fafb;
        }
        .recu {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            padding: 24px 28px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0 0 4px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .header p {
            margin: 2px 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
        }
        .header .ref {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.05em;
        }

        /* Body */
        .body { padding: 24px 28px 20px; }

        /* Section title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin: 20px 0 10px;
        }
        .section-title:first-child { margin-top: 0; }
        .section-title::before {
            content: '';
            width: 4px;
            height: 14px;
            background: #4f46e5;
            border-radius: 2px;
        }

        /* Rows */
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
            border-bottom: 1px dashed #f1f5f9;
        }
        .row:last-child { border-bottom: none; }
        .row .label {
            color: #6b7280;
            font-weight: 500;
        }
        .row .value {
            font-weight: 700;
            color: #111827;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .row .value.name {
            font-family: inherit;
            font-size: 13px;
        }

        /* Montant principal */
        .montant {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            margin: 12px 0 16px;
        }
        .montant .montant-label {
            font-size: 10px;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .montant .montant-value {
            font-size: 26px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.02em;
        }
        .montant .montant-value small {
            font-size: 14px;
            font-weight: 600;
            margin-left: 4px;
        }

        /* Bilan (total vs reste) */
        .bilan {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 6px;
        }
        .bilan-item {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            text-align: center;
        }
        .bilan-item .bilan-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .bilan-item .bilan-value {
            font-size: 15px;
            font-weight: 800;
        }
        .bilan-item.total .bilan-value { color: #4f46e5; }
        .bilan-item.reste { background: #fef2f2; border-color: #fecaca; }
        .bilan-item.reste .bilan-value { color: #e11d48; }
        .bilan-item.reste.is-zero { background: #ecfdf5; border-color: #a7f3d0; }
        .bilan-item.reste.is-zero .bilan-value { color: #059669; }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer strong { color: #4f46e5; font-weight: 700; }

        /* Actions */
        .actions {
            text-align: center;
            padding: 16px 28px 24px;
            background: #f9fafb;
            border-top: 1px solid #f1f5f9;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
            font-family: inherit;
        }
        .btn-print:hover { background: #4338ca; }
        .btn-print svg { width: 16px; height: 16px; }

        /* Print */
        @media print {
            body { padding: 0; background: #fff; }
            .recu {
                max-width: 100%;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }
            .header { background: #4f46e5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .montant { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bilan-item.reste { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="recu">
    {{-- Header --}}
    <div class="header">
        <h1>{{ $etablissement?->nom ?? 'EduManager' }}</h1>
        <p>Reçu de paiement de scolarité</p>
        <span class="ref">{{ $recu['reference'] ?? 'N/R' }}</span>
    </div>

    <div class="body">

        {{-- Élève --}}
        <div class="section-title">Élève</div>
        <div class="row">
            <span class="label">Nom</span>
            <span class="value name">{{ $recu['eleve'] ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="label">Matricule</span>
            <span class="value">{{ $recu['matricule'] ?? '—' }}</span>
        </div>

        {{-- Montant principal --}}
        <div class="section-title">Paiement</div>
        <div class="montant">
            <div class="montant-label">Montant payé</div>
            <div class="montant-value">
                {{ number_format((int) ($recu['montant'] ?? 0), 0, ',', ' ') }}<small>FCFA</small>
            </div>
        </div>

        <div class="row">
            <span class="label">Méthode</span>
            <span class="value name">{{ $recu['methode'] ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="label">Date</span>
            <span class="value name">{{ $recu['date'] ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="label">Référence</span>
            <span class="value">{{ $recu['reference'] ?? '—' }}</span>
        </div>

        {{-- Bilan --}}
        <div class="section-title">Scolarité</div>
        <div class="bilan">
            <div class="bilan-item total">
                <div class="bilan-label">Montant total</div>
                <div class="bilan-value">{{ number_format((int) ($recu['total'] ?? 0), 0, ',', ' ') }} F</div>
            </div>
            <div class="bilan-item reste {{ (int) ($recu['reste'] ?? 0) === 0 ? 'is-zero' : '' }}">
                <div class="bilan-label">Reste à payer</div>
                <div class="bilan-value">{{ number_format((int) ($recu['reste'] ?? 0), 0, ',', ' ') }} F</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p style="margin:0 0 4px;">Merci de votre confiance. Ce reçu fait foi de paiement.</p>
            <p style="margin:0;">Généré le <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="actions no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimer le reçu
        </button>
    </div>
</div>

</body>
</html>