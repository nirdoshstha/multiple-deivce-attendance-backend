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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('leave_type_id')->nullable()->constrained('leave_types');
            $table->string('date_from');
            $table->string('date_to')->nullable();
            $table->integer('total_days');
            $table->enum('day_type', ['full_day', 'half_day'])->default('full_day');
            $table->mediumText('reason');
            $table->unsignedInteger('is_approved')->default(0)->comment('0=pending, 1=approved, 2=rejected');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->string('approval_authorized_user')->nullable();
            $table->foreignId('approval_authorized_by')->nullable()->constrained('users');
            $table->string('approved_at')->nullable();
            $table->mediumText('approval_remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
