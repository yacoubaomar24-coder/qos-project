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
            // 1. Supprimer d'abord la contrainte unique
            $table->dropUnique(['adresse_mac']); 

            // 2. Supprimer ensuite la colonne
            $table->dropColumn('adresse_mac');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositifs', function (Blueprint $table) {
            $table->string('adresse_mac')->unique()->nullable()->after('nom');
        });
    }
};
