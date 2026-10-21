<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('contract_tickets')
            ->where('category', 'novo_contrato')
            ->update(['category' => 'novo_processo']);
    }

    public function down(): void
    {
        DB::table('contract_tickets')
            ->where('category', 'novo_processo')
            ->update(['category' => 'novo_contrato']);
    }
};
