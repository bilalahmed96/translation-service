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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_key_id')
                ->constrained('translation_keys')
                ->cascadeOnDelete();

            $table->foreignId('locale_id')
                ->constrained('locales');

            $table->text('content');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->unique(['translation_key_id', 'locale_id']);
            $table->index(['locale_id', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
