@extends('sadmin.layouts.guest')


@section('content')
<div class="w-full max-w-[500px]">
    <!-- Brand Identification -->
    <div class="flex flex-col items-center mb-8">
        <h1 class="text-3xl font-bold text-primary">EduManager</h1>
        <p class="text-sm text-on-surface-variant mt-1">Système de Gestion Centralisé</p>
    </div>

    <!-- Verification Card -->
    <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8 md:p-10">
        <div class="text-center mb-8">
            <h2 class="font-headline-md text-[22px] font-bold text-on-surface mb-3">Vérification du code</h2>
            <p class="font-body-md text-[15px] text-on-surface-variant leading-relaxed">
                Nous avons envoyé un code de vérification à votre adresse email. Veuillez le saisir ci-dessous.
            </p>
        </div>

        <!-- Message d'erreur (affichage fallback) -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-error-container/20 border border-error rounded-lg flex items-start gap-3">
                <span class="material-symbols-outlined text-error text-[20px]">error</span>
                <div>
                    <p class="font-label-md text-[14px] text-error">Code invalide</p>
                    @foreach ($errors->all() as $error)
                        <p class="font-body-sm text-[13px] text-error/80 mt-1">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Message de succès (affichage fallback) -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-secondary-container/20 border border-secondary rounded-lg flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
                <div>
                    <p class="font-label-md text-[14px] text-on-secondary-container">Code vérifié avec succès !</p>
                    <p class="font-body-sm text-[13px] text-on-secondary-container/80 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify.post') }}" class="space-y-8" id="otp-form">
            @csrf

            <!-- 6-Digit OTP Input Pattern -->
            <div class="flex justify-between gap-3" id="otp-container">
                <input type="text" name="otp[]" autocomplete="one-time-code" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="0" inputmode="numeric" maxlength="1">
                <input type="text" name="otp[]" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="1" inputmode="numeric" maxlength="1">
                <input type="text" name="otp[]" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="2" inputmode="numeric" maxlength="1">
                <input type="text" name="otp[]" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="3" inputmode="numeric" maxlength="1">
                <input type="text" name="otp[]" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="4" inputmode="numeric" maxlength="1">
                <input type="text" name="otp[]" class="otp-input w-12 h-14 md:w-14 md:h-16 text-center text-[20px] font-headline-md border border-outline rounded-lg bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all outline-none" data-index="5" inputmode="numeric" maxlength="1">
            </div>

            <div class="space-y-4">
                <!-- Primary Action -->
                <button type="submit" id="submit-btn" class="w-full h-12 bg-primary-container text-white rounded-lg font-label-md text-[15px] hover:bg-opacity-90 transition-all flex items-center justify-center gap-2 shadow-md shadow-primary-container/10">
                    <span>Vérifier le code</span>
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </button>

                <!-- Secondary Action -->
                <div class="text-center">
                    <button type="button" class="font-body-sm text-[13px] text-text-muted hover:text-primary transition-colors flex items-center justify-center gap-1 mx-auto" id="resend-btn">
                        <span class="material-symbols-outlined text-[16px]">refresh</span>
                        <span>Renvoyer le code</span>
                        <span class="text-[11px] opacity-70" id="timer"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer Link -->
    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-label-md text-[13px] text-primary hover:underline transition-all group">
            <span class="material-symbols-outlined text-[16px] transition-transform group-hover:-translate-x-1">arrow_back</span>
            <span>Retour à la connexion</span>
        </a>
    </div>

</div>


@endsection

@section('scripts')
<script>
    // SweetAlert (fallback si non chargé)
    function showSwal({ title, text = '', icon = 'info' } = {}) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({ title, text, icon });
        }
        // fallback simple
        alert((text ? (title + "\n" + text) : title));
    }

    @if ($errors->any())
        showSwal({
            title: 'Code OTP invalide',
            text: '{!! addslashes($errors->first()) !!}',
            icon: 'error'
        });
    @endif

    @if (session('success'))
        showSwal({
            title: 'Succès',
            text: '{!! addslashes(session('success')) !!}',
            icon: 'success'
        });
    @endif

    // OTP Input Logic
    const inputs = document.querySelectorAll('.otp-input');
    const otpForm = document.getElementById('otp-form');
    const submitBtn = document.getElementById('submit-btn');
    let isSubmitting = false;

    // Fonction pour récupérer le code complet
    function getFullOtpCode() {
        let code = '';
        inputs.forEach(input => {
            code += input.value;
        });
        return code;
    }

    // Fonction pour vérifier si tous les champs sont remplis
    function isOtpComplete() {
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value) allFilled = false;
        });
        return allFilled;
    }

    // Fonction pour soumettre automatiquement le formulaire
    function autoSubmit() {
        if (isOtpComplete() && !isSubmitting) {
            isSubmitting = true;
            
            // Désactiver le bouton et afficher le chargement
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> <span>Vérification en cours...</span>';
            
            // Soumettre le formulaire
            otpForm.submit();
        }
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            // Ne garder que les chiffres
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            
            // Vérifier si le code est complet pour soumettre automatiquement
            autoSubmit();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Gestion du collage
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const pastedNumbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
            
            for (let i = 0; i < pastedNumbers.length; i++) {
                if (inputs[i]) {
                    inputs[i].value = pastedNumbers[i];
                }
            }
            
            // Focus sur le dernier champ rempli
            const lastFilledIndex = Math.min(pastedNumbers.length, inputs.length - 1);
            if (inputs[lastFilledIndex]) {
                inputs[lastFilledIndex].focus();
            }
            
            // Vérifier automatiquement après le collage
            autoSubmit();
        });
    });

    // Timer pour renvoi du code
    let timeLeft = 59;
    const timerElement = document.getElementById('timer');
    const resendBtn = document.getElementById('resend-btn');

    if (timerElement && resendBtn) {
        const countdown = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerElement.innerText = '';
                resendBtn.disabled = false;
                resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                timeLeft--;
                timerElement.innerText = `(${timeLeft}s)`;
                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }, 1000);

        // Renvoi du code
        resendBtn.addEventListener('click', function() {
            if (!this.disabled) {
                // Réinitialiser le timer
                timeLeft = 59;
                
                // Désactiver le bouton de renvoi pendant l'envoi
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span> <span>Envoi...</span>';
                
                // Appel AJAX pour renvoyer le code (email uniquement depuis la session côté serveur)
                fetch('{{ route("verification.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher un message de succès temporaire
                        const toast = document.createElement('div');
                        toast.className = 'fixed bottom-4 right-4 bg-success-green text-white px-3 py-1.5 rounded-lg shadow-lg text-[11px] z-50';
                        toast.innerHTML = 'Code renvoyé avec succès !';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);
                        
                        // Réinitialiser les champs OTP
                        inputs.forEach(input => {
                            input.value = '';
                        });
                        inputs[0].focus();
                        
                        // Redémarrer le compte à rebours
                        let newTimeLeft = 59;
                        const newCountdown = setInterval(() => {
                            if (newTimeLeft <= 0) {
                                clearInterval(newCountdown);
                                timerElement.innerText = '';
                                resendBtn.disabled = false;
                                resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            } else {
                                newTimeLeft--;
                                timerElement.innerText = `(${newTimeLeft}s)`;
                                resendBtn.disabled = true;
                                resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            }
                        }, 1000);
                    }
                    resendBtn.innerHTML = originalText;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    resendBtn.innerHTML = originalText;
                    resendBtn.disabled = false;

                    // debug utile
                    const errText = document.createElement('div');
                    errText.className = 'mt-3 text-[12px] text-error';
                    errText.textContent = 'Impossible de renvoyer le code (vérifie la console).';
                    resendBtn.parentElement?.appendChild(errText);
                });
            }
        });
    }

    // Empêcher la soumission multiple
    otpForm.addEventListener('submit', function() {
        if (isSubmitting) {
            return false;
        }
        isSubmitting = true;
    });
</script>
@endsection