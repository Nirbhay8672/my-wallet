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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('website_name');
            $table->string('website_url');
            $table->string('website_logo_path');
            $table->integer('google_rank');
            $table->unsignedBigInteger('client_id');
            $table->bigInteger('time');
            $table->integer('total_update');
            $table->integer('is_backup_active')->comment('1 for active and 0 for inactive');
            $table->integer('total_site_helth');
            $table->integer('total_php_issue');
            $table->string('wp_admin_url');
            $table->string('payment_status')->default('Pending')->nullable();
            $table->string('package_type')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropForeign('websites_client_id_foreign');
        });
        Schema::dropIfExists('websites');
    }
};