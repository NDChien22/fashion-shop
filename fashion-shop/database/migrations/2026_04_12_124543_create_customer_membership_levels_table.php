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
        Schema::create('customer_membership_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('customer_code')->unique();
            $table->integer('membership_level_id')->unsigned();
            $table->bigInteger('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_membership_levels');
    }
};
