<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('campagne_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('categorie');
            $t->string('libelle');
            $t->decimal('montant', 14, 2);
            $t->date('date_depense');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
