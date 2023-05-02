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
        Schema::table('pins', function (Blueprint $table) {
            $table->fullText(['title', 'descreption']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->fullText(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->dropIndex('pins_title_descreption_fulltext');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_fulltext');
        });
    }
};
