<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rosca_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rosca_id');
            $table->unsignedBigInteger('round_id');
            $table->unsignedBigInteger('winner_member_id');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('rosca_id')->references('id')->on('roscas')->onDelete('cascade');
            $table->foreign('round_id')->references('id')->on('rosca_rounds')->onDelete('cascade');
            $table->foreign('winner_member_id')->references('id')->on('rosca_members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosca_payouts');
    }
};
