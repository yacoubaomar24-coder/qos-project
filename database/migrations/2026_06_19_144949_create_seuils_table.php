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
        Schema::create('seuils', function (Blueprint $table) {
            $table->id();
            // Seuil global ou par site
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            // Pourcentage d'insatisfaction déclenchant l'alerte (ex: 40)
            $table->integer('seuil_insatisfaction')->default(40);
            // Période d'évaluation en heures (ex: 24 = une journée)
            $table->integer('periode_heures')->default(24);
            // Notifications activées
            $table->boolean('notif_email')->default(true);
            $table->boolean('notif_sms')->default(false);
            // Email et téléphone de destination
            $table->string('email_destination')->nullable();
            $table->string('telephone_destination')->nullable();
            // Créé par quel utilisateur
            $table->foreignId('created_by')->nullable()->constrained('utilisateurs')->nullOnDelete();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seuils');
    }
};
