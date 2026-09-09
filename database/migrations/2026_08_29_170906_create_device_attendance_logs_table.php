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
        Schema::create('device_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_device_id')->nullable()->constrained('companies_devices')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staffs');
            $table->string('device_user_id')->nullable();   // raw id/PIN reported by the device
            $table->string('date');
            $table->time('time');
            $table->enum('attendance_type', ['auto', 'manual'])->default('manual');
            $table->unsignedTinyInteger('verify_type')->nullable(); // 1=fingerprint,4=card,...
            $table->unsignedTinyInteger('punch_state')->nullable(); // check-in/out/overtime etc.
            $table->boolean('processed')->default(false); // has this been folded into `attendances`?
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();


            $table->index(['staff_id', 'processed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_attendance_logs');
    }
};
