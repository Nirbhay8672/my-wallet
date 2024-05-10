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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('website_id')->nullable();
            $table->date('payment_date');
            $table->time('payment_time');
            $table->bigInteger('amount');
            $table->string('status')->nullable();
            $table->string('package_type')->nullable();
            $table->date('last_try')->nullable();
            $table->date('last_success')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};