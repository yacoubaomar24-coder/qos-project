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
        Schema::table('pays', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('id')
                ->constrained('utilisateurs')
                ->nullOnDelete();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('id')
                ->constrained('utilisateurs')
                ->nullOnDelete();
        });

        Schema::table('villes', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('id')
                ->constrained('utilisateurs')
                ->nullOnDelete();
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('id')
                ->constrained('utilisateurs')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pays', fn($t) => $t->dropColumn('created_by'));
        Schema::table('regions', fn($t) => $t->dropColumn('created_by'));
        Schema::table('villes', fn($t) => $t->dropColumn('created_by'));
        Schema::table('sites', fn($t) => $t->dropColumn('created_by'));
    }
};
