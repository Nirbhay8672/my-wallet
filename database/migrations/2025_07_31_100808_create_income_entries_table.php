<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('income_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('income_type_id');
            $table->decimal('amount', 15, 2);
            $table->string('proof')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('time');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('income_type_id')->references('id')->on('income_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_entries');
    }
};
