<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesees', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->date('date_pesee');
            $t->decimal('poids_moyen_kg', 8, 2);
            $t->unsignedInteger('effectif_pese');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesees');
    }
};
