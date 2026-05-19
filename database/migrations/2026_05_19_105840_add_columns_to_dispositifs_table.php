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
        Schema::table('dispositifs', function (Blueprint $table) {
            $table->timestamp('derniere_connexion')->nullable()->after('adresse_mac');
            $table->boolean('en_ligne')->default(false)->after('derniere_connexion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositifs', function (Blueprint $table) {
            $table->dropColumn(['derniere_connexion', 'en_ligne']);
        });
    }
};
