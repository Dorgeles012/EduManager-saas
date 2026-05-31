<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aucune modification DB automatique ici :
        // - ton affichage demande juste FC CFA + "créé par" dans l'UI
        // - la bonne source pour "créé par" dépend de ta structure (champ user_id, created_by, etc.)
        // Donc cette migration est volontairement vide.
    }

    public function down(): void
    {
        // No-op
    }
};

