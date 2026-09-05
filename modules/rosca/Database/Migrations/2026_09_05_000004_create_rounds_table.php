<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rosca_rounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rosca_id');
            $table->integer('round_number');
            $table->timestamp('due_date')->nullable();
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->unsignedBigInteger('winner_member_id')->nullable();
            $table->timestamps();

            $table->foreign('rosca_id')->references('id')->on('roscas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosca_rounds');
    }
};
