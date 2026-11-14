<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('intrant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('type');                         // stock_initial | entree | sortie | consommation | ajustement
            $t->decimal('quantite', 12, 2);             // signée : + entrée, - sortie
            $t->decimal('quantite_avant', 12, 2);
            $t->decimal('quantite_apres', 12, 2);
            $t->string('motif')->nullable();
            $t->date('date_mouvement');
            $t->timestamps();
            $t->index(['intrant_id', 'date_mouvement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
