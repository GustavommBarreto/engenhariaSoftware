<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            $table->string('nome');      // "Sprint 1", "Sprint Planejamento Release 2" etc.
            $table->text('objetivo');    // WHAT
            $table->text('trabalho')->nullable(); // HOW (estratégia / abordagem)
            $table->text('equipe')->nullable();   // WHO (quem participa)
            $table->text('incremento')->nullable(); // qual incremento se espera

            $table->date('inicio');
            $table->date('fim');

            // PLANEJADA, EM_ANDAMENTO, CONCLUIDA, CANCELADA
            $table->string('status')->default('PLANEJADA');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
