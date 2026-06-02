<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Suppression après migration de données.
        // Si certaines tables existent encore, on les supprime.
        Schema::dropIfExists('enseignants');
        Schema::dropIfExists('enseignant_matiere');
        Schema::dropIfExists('personnel');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('clients');

        // (Optionnel) si tu as d'autres tables liées anciennes, les ajouter ici.
    }

    public function down(): void
    {
        // volontairement vide
    }
};

