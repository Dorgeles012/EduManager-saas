@php
    /** @var string $otp */
@endphp

<div style="font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji','Segoe UI Emoji';">
    <h2 style="margin: 0 0 16px; font-size: 18px;">
        Votre code de vérification (OTP)
    </h2>

    <p style="margin: 0 0 12px; color: #374151; font-size: 14px;">
        Nous avons reçu une demande de réinitialisation de mot de passe. Utilisez le code ci-dessous pour continuer.
    </p>

    <div style="display: inline-block; padding: 10px 16px; font-size: 20px; letter-spacing: 4px; font-weight: 700; background: #EEF2FF; color: #3730A3; border-radius: 10px;">
        {{ $otp }}
    </div>

    <p style="margin: 16px 0 0; color: #6B7280; font-size: 12.5px;">
        Ce code expire dans environ 10 minutes. Ne le partagez avec personne.
    </p>
</div>

