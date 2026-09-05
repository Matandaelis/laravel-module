<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rosca_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rosca_id');
            $table->unsignedBigInteger('member_id');
            $table->decimal('amount', 12, 2);
            $table->timestamp('contributed_at')->nullable();
            $table->timestamps();

            $table->foreign('rosca_id')->references('id')->on('roscas')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosca_contributions');
    }
};
