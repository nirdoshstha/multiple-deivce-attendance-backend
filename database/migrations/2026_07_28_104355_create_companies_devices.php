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
        Schema::create('companies_devices', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->bigInteger('company_id');
            $table->bigInteger('device_brand_id');
            $table->bigInteger('device_id');
            $table->bigInteger('serial_no');
            $table->bigInteger('port');
            $table->bigInteger('api_key');

            $table->string('device_code');
            $table->string('api_url');
            $table->string('ip');

            $table->boolean('status')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_devices');
    }
};
