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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('manager_email', 255)->nullable()->after('manager');
            $table->string('deputy_manager_email', 255)->nullable()->after('deputy_manager');
            $table->string('inspector_email', 255)->nullable()->after('inspector');
            $table->string('deputy_inspector_email', 255)->nullable()->after('deputy_inspector');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'manager_email',
                'deputy_manager_email',
                'inspector_email',
                'deputy_inspector_email'
            ]);
        });
    }
};
