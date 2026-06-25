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
        Schema::create('rapports_auto', function (Blueprint $table) {
            $table->id();
            // Fréquence : quotidien, hebdomadaire, mensuel
            $table->enum('frequence', ['quotidien', 'hebdomadaire', 'mensuel']);
            // Sites inclus — null = tous les sites accessibles
            $table->json('site_ids')->nullable();
            // Email de destination
            $table->string('email_destination');
            // Actif ou non
            $table->boolean('actif')->default(true);
            // Créé par
            $table->foreignId('created_by')->nullable()->constrained('utilisateurs')->nullOnDelete();
            // Dernière exécution
            $table->timestamp('derniere_execution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports_auto');
    }
};
