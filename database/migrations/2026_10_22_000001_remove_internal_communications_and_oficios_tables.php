<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('internal_communications');
        Schema::dropIfExists('oficios');
    }

    public function down(): void
    {
        Schema::create('internal_communications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('year', 4);
            $table->date('date');
            $table->string('sender_name');
            $table->string('sender_role')->nullable();
            $table->string('department')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_role')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('year', 4);
            $table->date('date');
            $table->string('sender_name');
            $table->string('sender_role')->nullable();
            $table->string('department')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_role')->nullable();
            $table->string('recipient_institution')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
