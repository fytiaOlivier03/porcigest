<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->string('code');
            $t->string('nom');
            $t->string('type');                         // maternite, engraissement, quarantaine...
            $t->unsignedInteger('capacite');
            $t->string('localisation')->nullable();
            $t->string('etat')->default('disponible');  // disponible | occupee | maintenance
            $t->timestamps();
            $t->unique(['exploitation_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unites');
    }
};
