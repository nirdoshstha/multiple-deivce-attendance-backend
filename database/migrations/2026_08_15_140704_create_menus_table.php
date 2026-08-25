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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('slug');
            $table->unsignedInteger('rank');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->nullable()->comment('true:Main Menu');
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
