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
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->dropColumn('uploaded_file');
        });

        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diary_entry_id')->references('id')->on('diary_entries')->cascadeOnDelete();
            $table->text('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->text('uploaded_file')->nullable()->default(null);
        });

        Schema::dropIfExists('uploaded_files');
    }
};
