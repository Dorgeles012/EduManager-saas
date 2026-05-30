<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = (string) $request->input('email');

        // Ne pas révéler si l'email existe.
        $user = User::query()->where('email', $email)->first();

        // Génération token sécurisé
        $token = Str::random(64);

        if ($user) {
            // Stockage token dans password_reset_tokens (table Laravel existante dans votre migration)
            // Table: password_reset_tokens (email PK)
            
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ]
            );

            try {
                $this->sendResetEmailPHPMailer($email, $token);
                return back()->with('status', 'Un lien de réinitialisation a été envoyé si l’adresse existe dans notre système.');
            } catch (Throwable $e) {
                report($e);
                return back()->withErrors(['email' => 'Erreur d’envoi du mail de réinitialisation (SMTP). Veuillez réessayer.']);
            }
        }

        return back()->with('status', 'Un lien de réinitialisation a été envoyé si l’adresse existe dans notre système.');
    }

    private function sendResetEmailPHPMailer(string $email, string $token): void
    {
        $resetUrl = route('password.reset', ['token' => $token, 'email' => urlencode($email)]);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';

        $mail->Host = env('MAIL_HOST');
        $mail->SMTPAuth = (bool) env('MAIL_USERNAME');
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->Port = (int) env('MAIL_PORT', 587);

        $scheme = env('MAIL_ENCRYPTION', 'tls');
        if ($scheme === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $fromAddress = env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME', 'hello@example.com'));
        $fromName = env('MAIL_FROM_NAME', env('APP_NAME', 'EduManager'));

        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation du mot de passe';

        $body = view('emails.password_reset', [
            'resetUrl' => $resetUrl,
            'token' => $token,
            'appName' => env('APP_NAME', 'EduManager'),
        ])->render();

        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        if (!$mail->send()) {
            throw new \RuntimeException('PHPMailer: impossible d’envoyer le mail de réinitialisation');
        }
    }
}

