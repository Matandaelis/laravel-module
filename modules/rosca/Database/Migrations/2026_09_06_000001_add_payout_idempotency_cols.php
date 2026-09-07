<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rosca_payouts', function (Blueprint $table) {
            if (! Schema::hasColumn('rosca_payouts', 'idempotency_key')) {
                $table->string('idempotency_key')->nullable()->after('id');
            }

            if (! Schema::hasColumn('rosca_payouts', 'external_transaction_id')) {
                $table->string('external_transaction_id')->nullable()->after('idempotency_key');
            }

            if (! Schema::hasColumn('rosca_payouts', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rosca_payouts', function (Blueprint $table) {
            if (Schema::hasColumn('rosca_payouts', 'idempotency_key')) {
                $table->dropColumn('idempotency_key');
            }
            if (Schema::hasColumn('rosca_payouts', 'external_transaction_id')) {
                $table->dropColumn('external_transaction_id');
            }
            if (Schema::hasColumn('rosca_payouts', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }
};
