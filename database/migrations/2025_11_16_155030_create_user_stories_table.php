<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_stories', function (Blueprint $table) {
            $table->id();

            // Projeto dono da história
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // Sprint à qual a história pertence (sprint backlog) - opcional
            $table->foreignId('sprint_id')->nullable()
                  ->constrained()->nullOnDelete();

            // Descrição da história
            $table->string('titulo');                     // "Como PO eu quero..."
            $table->text('descricao')->nullable();        // detalhes
            $table->text('criterios_aceite')->nullable(); // critérios de aceite

            // Campos de priorização / planejamento
            $table->unsignedSmallInteger('ordem')->default(0);      // posição no backlog
            $table->unsignedTinyInteger('story_points')->nullable(); // esforço
            $table->unsignedTinyInteger('valor_negocio')->nullable(); // valor para negócio

            // Status simples da história dentro do fluxo
            // BACKLOG, EM_SPRINT, CONCLUIDA, CANCELADA
            $table->string('status')->default('BACKLOG');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stories');
    }
};
