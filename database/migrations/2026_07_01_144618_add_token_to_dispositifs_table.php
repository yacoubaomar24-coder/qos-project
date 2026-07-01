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
            // Token unique d'identification du dispositif
            $table->string('token', 64)->unique()->nullable()->after('adresse_mac');
            // Date de génération du token
            $table->timestamp('token_genere_le')->nullable()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispositifs', function (Blueprint $table) {
            $table->dropColumn(['token', 'token_genere_le']);
        });
    }
};
