<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('type');
            $t->date('date_activite');
            $t->text('description')->nullable();
            $t->decimal('cout', 12, 2)->default(0);
            $t->timestamps();
            $t->index(['campagne_id', 'date_activite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
