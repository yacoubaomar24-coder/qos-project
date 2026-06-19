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
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            // Site concerné par l'alerte
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            // Seuil qui a déclenché l'alerte
            $table->foreignId('seuil_id')->nullable()->constrained('seuils')->nullOnDelete();
            // Taux d'insatisfaction au moment de l'alerte
            $table->decimal('taux_insatisfaction', 5, 2);
            // Seuil configuré au moment de l'alerte
            $table->integer('seuil_configure');
            // Nombre total de votes sur la période
            $table->integer('total_votes');
            // Statut de l'alerte
            $table->enum('statut', ['nouvelle', 'vue', 'resolue'])->default('nouvelle');
            // Notifications envoyées
            $table->boolean('email_envoye')->default(false);
            $table->boolean('sms_envoye')->default(false);
            // Message de l'alerte
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
