<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->renameColumn('libelle_neutre', 'libelle_moyen');
            $table->renameColumn('couleur_neutre', 'couleur_moyen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->renameColumn('libelle_moyen', 'libelle_neutre');
            $table->renameColumn('couleur_moyen', 'couleur_neutre');
        });
    }
};
