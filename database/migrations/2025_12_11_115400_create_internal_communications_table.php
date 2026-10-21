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
        Schema::create('internal_communications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Número da CI (ex: 001/2025)
            $table->string('year', 4);
            $table->date('date');

            // Remetente
            $table->string('sender_name');
            $table->string('sender_role')->nullable(); // Cargo do remetente
            $table->string('department')->nullable(); // Setor de origem

            // Destinatário
            $table->string('recipient_name');
            $table->string('recipient_role')->nullable(); // Cargo do destinatário

            // Conteúdo
            $table->string('subject'); // Assunto
            $table->text('content'); // Corpo da mensagem

            // Usuário que criou
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_communications');
    }
};
