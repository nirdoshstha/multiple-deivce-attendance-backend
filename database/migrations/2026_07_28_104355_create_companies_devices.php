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
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('device_brand_id')->constrained('device_brands');
            $table->foreignId('device_id')->constrained('devices');
            $table->string('serial_no');
            $table->bigInteger('port')->nullable();
            $table->bigInteger('api_key')->nullable();

            $table->string('device_code')->nullable();
            $table->string('api_url')->nullable();
            $table->string('ip')->nullable();

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
