<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contract_tickets', function (Blueprint $table) {
            $table->string('dotacao', 100)->nullable()->after('ficha');
        });
    }

    public function down(): void
    {
        Schema::table('contract_tickets', function (Blueprint $table) {
            $table->dropColumn('dotacao');
        });
    }
};
