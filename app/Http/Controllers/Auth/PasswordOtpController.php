<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Services\RoleDashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class PasswordOtpController extends Controller
{
    private const OTP_TTL_SECONDS = 600;
    private const OTP_ATTEMPTS_MAX = 5;
    private const OTP_LOCK_SECONDS = 300;

    private const OTP_SESSION_EMAIL_KEY = 'password_otp_email';
    private const OTP_SESSION_VERIFIED_KEY = 'password_otp_verified';
    private const OTP_SESSION_ATTEMPTS_KEY = 'password_otp_attempts';
    private const OTP_SESSION_LOCK_UNTIL_KEY = 'password_otp_lock_until';
    private const PASSWORD_RESET_EMAIL_KEY = 'password_reset_email';

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            ], [
                'email.exists' => 'Aucun utilisateur ne correspond à cette adresse email.',
            ]);

            $email = (string) $validated['email'];

            $lockUntil = (int) $request->session()->get(self::OTP_SESSION_LOCK_UNTIL_KEY, 0);
            if ($lockUntil > now()->timestamp) {
                $remaining = max(1, $lockUntil - now()->timestamp);

                return back()->withErrors(['otp' => "Trop de tentatives. Réessayez dans {$remaining}s."])->withInput();
            }

            $otpCode = (string) random_int(100000, 999999);
            $expiresAt = Carbon::now()->addSeconds(self::OTP_TTL_SECONDS);

            PasswordOtp::query()->where('email', $email)->delete();
            PasswordOtp::query()->create([
                'email' => $email,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
            ]);

            $request->session()->put(self::OTP_SESSION_EMAIL_KEY, $email);
            $request->session()->forget([
                self::OTP_SESSION_VERIFIED_KEY,
                self::PASSWORD_RESET_EMAIL_KEY,
                self::OTP_SESSION_ATTEMPTS_KEY,
                self::OTP_SESSION_LOCK_UNTIL_KEY,
            ]);

            $this->sendOtpWithPhpMailer($email, $otpCode);

            return redirect()->route('password.otp.verify.get')
                ->with('status', 'Un code de vérification a été envoyé à votre email.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (PHPMailerException $e) {
            if ($request->filled('email')) {
                PasswordOtp::query()->where('email', (string) $request->input('email'))->delete();
            }

            Log::error('Password OTP email failed', [
                'email' => $request->input('email'),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['email' => "Impossible d'envoyer le code OTP. Vérifiez la configuration email."])
                ->withInput();
        }
    }

    public function verifyGet(Request $request): View|RedirectResponse
    {
        $email = (string) $request->session()->get(self::OTP_SESSION_EMAIL_KEY, '');

        if ($email === '') {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expirée. Recommencez la demande de code.']);
        }

        return view('auth.verify-email', ['email' => $email]);
    }

    public function verifyPost(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'string', 'regex:/^\d$/'],
        ]);

        $email = (string) $request->session()->get(self::OTP_SESSION_EMAIL_KEY, '');
        if ($email === '') {
            return back()->withErrors(['otp' => 'Session expirée. Recommencez la demande de code.'])->withInput();
        }

        $lockUntil = (int) $request->session()->get(self::OTP_SESSION_LOCK_UNTIL_KEY, 0);
        if ($lockUntil > now()->timestamp) {
            $remaining = max(1, $lockUntil - now()->timestamp);

            return back()->withErrors(['otp' => "Trop de tentatives. Réessayez dans {$remaining}s."])->withInput();
        }

        $otpCode = implode('', (array) $request->input('otp', []));
        $record = PasswordOtp::query()
            ->where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>=', Carbon::now())
            ->latest('id')
            ->first();

        if (! $record) {
            $attempts = (int) $request->session()->get(self::OTP_SESSION_ATTEMPTS_KEY, 0) + 1;
            $request->session()->put(self::OTP_SESSION_ATTEMPTS_KEY, $attempts);

            if ($attempts >= self::OTP_ATTEMPTS_MAX) {
                $request->session()->put(self::OTP_SESSION_LOCK_UNTIL_KEY, now()->timestamp + self::OTP_LOCK_SECONDS);
            }

            return back()->withErrors(['otp' => 'Code OTP invalide.'])->withInput();
        }

        $request->session()->put(self::OTP_SESSION_VERIFIED_KEY, true);
        $request->session()->put(self::PASSWORD_RESET_EMAIL_KEY, $email);
        $request->session()->forget(self::OTP_SESSION_ATTEMPTS_KEY);

        return redirect()->route('password.otp.reset.get');
    }

    public function resend(Request $request): JsonResponse
    {
        $email = (string) $request->session()->get(self::OTP_SESSION_EMAIL_KEY, '');

        if ($email === '') {
            return response()->json(['success' => false, 'message' => 'Session expirée.'], 419);
        }

        if (! User::query()->where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        try {
            $otpCode = (string) random_int(100000, 999999);
            $expiresAt = Carbon::now()->addSeconds(self::OTP_TTL_SECONDS);

            PasswordOtp::query()->where('email', $email)->delete();
            PasswordOtp::query()->create([
                'email' => $email,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
            ]);

            $request->session()->forget([
                self::OTP_SESSION_VERIFIED_KEY,
                self::PASSWORD_RESET_EMAIL_KEY,
                self::OTP_SESSION_ATTEMPTS_KEY,
                self::OTP_SESSION_LOCK_UNTIL_KEY,
            ]);

            $this->sendOtpWithPhpMailer($email, $otpCode);

            return response()->json(['success' => true]);
        } catch (PHPMailerException $e) {
            Log::error('Password OTP resend failed', [
                'email' => $email,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => "Impossible d'envoyer le code OTP."], 500);
        }
    }

    public function resetGet(Request $request): View|RedirectResponse
    {
        $email = (string) $request->session()->get(self::PASSWORD_RESET_EMAIL_KEY, '');

        if ($email === '' || ! $request->session()->get(self::OTP_SESSION_VERIFIED_KEY)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Veuillez vérifier votre code avant de réinitialiser le mot de passe.']);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ]);

        $email = (string) $request->session()->get(self::PASSWORD_RESET_EMAIL_KEY, '');
        if ($email === '' || ! $request->session()->get(self::OTP_SESSION_VERIFIED_KEY)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session invalide. Veuillez recommencer.']);
        }

        $user = User::query()->where('email', $email)->first();
        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        PasswordOtp::query()->where('email', $email)->delete();

        $request->session()->forget([
            self::OTP_SESSION_VERIFIED_KEY,
            self::OTP_SESSION_EMAIL_KEY,
            self::PASSWORD_RESET_EMAIL_KEY,
            self::OTP_SESSION_ATTEMPTS_KEY,
            self::OTP_SESSION_LOCK_UNTIL_KEY,
        ]);

        $routeName = app(RoleDashboardService::class)->routeNameFor($user);
        if (! $routeName) {
            return redirect()->route('login')->withErrors(['email' => 'Rôle utilisateur non autorisé.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($routeName)->with('status', 'Mot de passe réinitialisé avec succès.');
    }

    /**
     * @throws PHPMailerException
     */
    private function sendOtpWithPhpMailer(string $email, string $otpCode): void
    {
        $smtp = config('mail.mailers.smtp', []);
        $from = config('mail.from', []);

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = (string) ($smtp['host'] ?? '127.0.0.1');
        $mail->Port = (int) ($smtp['port'] ?? 2525);

        $username = (string) ($smtp['username'] ?? '');
        if ($username !== '') {
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = (string) ($smtp['password'] ?? '');
        }

        $scheme = (string) ($smtp['scheme'] ?? '');
        if ($scheme === 'smtps' || $mail->Port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($scheme === 'tls' || $mail->Port === 587) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $fromAddress = (string) ($from['address'] ?? 'hello@example.com');
        $fromName = (string) ($from['name'] ?? config('app.name', 'EduManager'));

        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Votre code de vérification (OTP) - EduManager';
        $mail->Body = view('emails.password-otp', ['otp' => $otpCode])->render();
        $mail->AltBody = "Votre code OTP EduManager est : {$otpCode}";
        $mail->send();
    }
}
