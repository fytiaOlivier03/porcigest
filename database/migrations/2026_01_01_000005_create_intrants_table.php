<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intrants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exploitation_id')->constrained()->cascadeOnDelete();
            $t->string('nom');
            $t->string('categorie');                    // aliment, medicament, produit_veterinaire, consommable, carburant
            $t->string('unite_mesure', 20);             // kg, L, dose...
            $t->decimal('quantite_disponible', 12, 2)->default(0);
            $t->decimal('seuil_minimum', 12, 2)->default(0);
            $t->decimal('cout_unitaire', 12, 2)->default(0);   // en Ariary
            $t->timestamps();
            $t->unique(['exploitation_id', 'nom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intrants');
    }
};
