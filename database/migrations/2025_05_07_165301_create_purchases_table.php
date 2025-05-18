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
            $table->foreignId('education_stage_subject_id')->constrained()->onDelete('cascade');
            $table->integer('remaining_credits')->default(0); // الحصص المتبق
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
