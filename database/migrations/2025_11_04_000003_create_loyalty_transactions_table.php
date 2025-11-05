<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('loyalty_transactions', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('user_id');
      $table->decimal('amount', 10, 2);
      $table->string('type');
      $table->integer('points_earned');
      $table->string('reference');
      $table->timestamp('created_at');

      // Index for user_id
      $table->index('user_id');

      // Unique index for transaction reference to ensure idempotency
      $table->unique('reference');

      // Index for type+user_id queries
      $table->index(['type', 'user_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('loyalty_transactions');
  }
};
