<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagnes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('unite_id')->constrained()->restrictOnDelete();
            $t->string('nom');
            $t->string('production');                   // ex : Porcs charcutiers
            $t->date('date_debut');
            $t->date('date_fin_prevue');
            $t->date('date_fin_reelle')->nullable();
            $t->unsignedInteger('effectif_initial');
            $t->string('statut')->default('en_cours');  // planifiee | en_cours | terminee | annulee
            $t->timestamps();
            $t->index(['exploitation_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};
