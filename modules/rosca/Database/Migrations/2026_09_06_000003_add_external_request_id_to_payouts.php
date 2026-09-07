<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rosca_payouts', function (Blueprint $table) {
            if (! Schema::hasColumn('rosca_payouts', 'external_request_id')) {
                $table->string('external_request_id')->nullable()->after('idempotency_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rosca_payouts', function (Blueprint $table) {
            if (Schema::hasColumn('rosca_payouts', 'external_request_id')) {
                $table->dropColumn('external_request_id');
            }
        });
    }
};
