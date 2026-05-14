<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->enum('status', [
                'uploaded',
                'parsing',
                'categorising',
                'saving',
                'reviewing',
                'imported',
            ])->default('uploaded');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statements');
    }
};
