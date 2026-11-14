<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $t->date('date_production');
            $t->unsignedInteger('nombre_porcs');        // porcs sortis / vendus
            $t->decimal('poids_total_kg', 10, 2);       // poids vif total produit
            $t->string('observation')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
