<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rosca_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rosca_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('payout_id')->nullable();
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 12, 2);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('rosca_id')->references('id')->on('roscas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosca_ledger');
    }
};
