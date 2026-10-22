<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('contract_files');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('contract_types');
    }

    public function down(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_name');
            $table->string('trade_name')->nullable();
            $table->string('tax_id', 20)->unique();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('contract_type_id')->constrained()->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total', 15, 2);
            $table->string('manager')->nullable();
            $table->string('deputy_manager')->nullable();
            $table->string('inspector')->nullable();
            $table->string('deputy_inspector')->nullable();
            $table->string('manager_email')->nullable();
            $table->string('deputy_manager_email')->nullable();
            $table->string('inspector_email')->nullable();
            $table->string('deputy_inspector_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path', 500);
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamps();
        });
    }
};
