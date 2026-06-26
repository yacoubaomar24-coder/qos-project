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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            // -----------------------------------------------
            // Libellés des boutons du dispositif IoT
            // -----------------------------------------------
            $table->string('libelle_satisfait')->default('Satisfait');
            $table->string('libelle_neutre')->default('Moyennement satisfait');
            $table->string('libelle_insatisfait')->default('Insatisfait');

            // -----------------------------------------------
            // Couleurs des boutons
            // -----------------------------------------------
            $table->string('couleur_satisfait')->default('#22c55e');
            $table->string('couleur_neutre')->default('#f59e0b');
            $table->string('couleur_insatisfait')->default('#ef4444');

            // -----------------------------------------------
            // Plages horaires d'activité des dispositifs
            // ex: 08:00 → 17:00
            // -----------------------------------------------
            $table->time('heure_debut')->default('08:00:00');
            $table->time('heure_fin')->default('17:00:00');

            // Jours actifs — JSON ex: [1,2,3,4,5] = Lun→Ven
            $table->json('jours_actifs')->default('[1,2,3,4,5]');

            // -----------------------------------------------
            // Personnalisation de l'interface
            // -----------------------------------------------
            $table->string('organisation_nom')->default('Mon Organisation');
            $table->string('organisation_logo')->nullable(); // chemin du logo
            $table->string('couleur_primaire')->default('#f59e0b');  // amber
            $table->string('couleur_secondaire')->default('#111827'); // dark

            // -----------------------------------------------
            // Appartient à quel super admin / organisation
            // -----------------------------------------------
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('utilisateurs')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
