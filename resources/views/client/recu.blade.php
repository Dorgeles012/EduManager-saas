@php
    $recu = $recu ?? [];
    $etablissement = $etablissement ?? null;
@endphp
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reçu de paiement</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111c2d; margin: 0; padding: 24px; background: #fff; }
        .recu { max-width: 480px; margin: 0 auto; border: 1px solid #c8c4d5; border-radius: 12px; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #1f108e; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { color: #1f108e; margin: 0; font-size: 22px; }
        .header p { margin: 4px 0; color: #464553; font-size: 13px; }
        .section-title { font-size: 12px; text-transform: uppercase; color: #777584; letter-spacing: 1px; margin: 16px 0 8px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; border-bottom: 1px dashed #e7eeff; }
        .row .label { color: #464553; }
        .row .value { font-weight: bold; }
        .total { display: flex; justify-content: space-between; padding: 12px 0; font-size: 16px; font-weight: bold; color: #006a61; }
        .footer { text-align: center; margin-top: 24px; font-size: 11px; color: #777584; }
        .montant { background: #f0f3ff; border-radius: 8px; padding: 12px; text-align: center; font-size: 24px; font-weight: bold; color: #1f108e; margin: 12px 0; }
        @media print { body { padding: 0; } .recu { border: none; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="recu">
        <div class="header">
            <h1>{{ $etablissement?->nom ?? 'EduManager' }}</h1>
            <p>Reçu de paiement de scolarité</p>
            <p>{{ $recu['reference'] ?? 'N/R' }}</p>
        </div>

        <div class="section-title">Élève</div>
        <div class="row"><span class="label">Nom</span><span class="value">{{ $recu['eleve'] ?? '—' }}</span></div>
        <div class="row"><span class="label">Matricule</span><span class="value">{{ $recu['matricule'] ?? '—' }}</span></div>

        <div class="section-title">Paiement</div>
        <div class="montant">{{ number_format((int) ($recu['montant'] ?? 0), 0, ',', ' ') }} FCFA</div>
        <div class="row"><span class="label">Méthode</span><span class="value">{{ $recu['methode'] ?? '—' }}</span></div>
        <div class="row"><span class="label">Date</span><span class="value">{{ $recu['date'] ?? '—' }}</span></div>
        <div class="row"><span class="label">Référence</span><span class="value">{{ $recu['reference'] ?? '—' }}</span></div>

        <div class="section-title">Scolarité</div>
        <div class="row"><span class="label">Montant total</span><span class="value">{{ number_format((int) ($recu['total'] ?? 0), 0, ',', ' ') }} FCFA</span></div>
        <div class="row"><span class="label">Reste à payer</span><span class="value">{{ number_format((int) ($recu['reste'] ?? 0), 0, ',', ' ') }} FCFA</span></div>

        <div class="footer">
            <p>Merci de votre confiance. Ce reçu fait foi de paiement.</p>
            <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <div style="text-align:center; margin-top:20px;" class="no-print">
            <button onclick="window.print()" style="background:#1f108e;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Imprimer</button>
        </div>
    </div>
</body>
</html>
