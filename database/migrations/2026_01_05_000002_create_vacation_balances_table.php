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
        Schema::create('vacation_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('year', 4);

            // Controle de dias
            $table->integer('total_days')->default(30);
            $table->integer('used_days')->default(0);
            $table->integer('remaining_days')->default(30);

            $table->timestamps();

            // Constraint para garantir apenas um registro por usuário/ano
            $table->unique(['user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_balances');
    }
};
