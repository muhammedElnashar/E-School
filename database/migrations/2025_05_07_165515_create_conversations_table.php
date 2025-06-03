<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
            $table->string('last_message')->nullable(); // آخر رسالة في المحادثة
            $table->timestamps();
            $table->unique(['user_one_id', 'user_two_id']);

            // فهارس للبحث السريع
            $table->index(['user_one_id', 'updated_at']);
            $table->index(['user_two_id', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
