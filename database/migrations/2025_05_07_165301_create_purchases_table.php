<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط الطالب
            $table->foreignId('marketplace_item_id')->constrained()->onDelete('cascade'); // ربط بالباكدج
            $table->integer('remaining_credits')->default(0);
            $table->decimal('price', 8, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};
