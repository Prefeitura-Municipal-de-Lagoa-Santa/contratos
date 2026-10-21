<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacation_requests', function (Blueprint $table) {
            $table->id();

            // Solicitante
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Período de férias
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('period_days'); // 15 ou 30

            // Status e aprovação
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado', 'cancelado'])->default('pendente');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Observações
            $table->text('observations')->nullable();

            // Ano da solicitação para controle
            $table->string('year', 4);

            $table->timestamps();

            // Índices para consultas frequentes
            $table->index(['user_id', 'year']);
            $table->index(['status', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_requests');
    }
};
