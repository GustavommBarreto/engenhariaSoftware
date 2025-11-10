<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Dono do projeto (quem criou)
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            // Campos básicos
            $table->string('nome');
            $table->text('descricao')->nullable();

            // status: 'Ativo', 'Pausado', 'Concluído'
            $table->string('status')->default('Ativo');

            // Datas
            $table->date('inicio');
            $table->date('fim')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
