<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contract_tickets', function (Blueprint $table) {
            $table->string('demandante', 255)->nullable();
            $table->string('responsavel', 255)->nullable();
            $table->string('matricula', 100)->nullable();
            $table->string('demandado', 255)->nullable();
            $table->text('solucao_sugerida')->nullable();
            $table->decimal('valor_estimado', 15, 2)->nullable();
            $table->date('data_demanda')->nullable();
            $table->string('duracao_contrato', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contract_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'demandante',
                'responsavel',
                'matricula',
                'demandado',
                'solucao_sugerida',
                'valor_estimado',
                'data_demanda',
                'duracao_contrato',
            ]);
        });
    }
};
