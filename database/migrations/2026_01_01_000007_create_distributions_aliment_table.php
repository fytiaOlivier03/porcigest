<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions_aliment', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $t->foreignId('intrant_id')->constrained()->restrictOnDelete();
            $t->foreignId('mouvement_stock_id')->constrained('mouvements_stock')->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->date('date_distribution');
            $t->decimal('quantite', 12, 2);
            $t->unsignedInteger('nombre_porcs');
            $t->string('observation')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions_aliment');
    }
};
