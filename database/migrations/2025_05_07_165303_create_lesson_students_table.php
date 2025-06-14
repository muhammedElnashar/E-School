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
        Schema::create('lesson_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_occurrence_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->boolean('is_paid_to_teacher')->default(false);
            $table->timestamps();
            $table->unique(['lesson_occurrence_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_students');
    }
};
