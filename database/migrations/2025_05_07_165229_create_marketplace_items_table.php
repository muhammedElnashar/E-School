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
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('education_stage_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('package_scope')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('lecture_credits')->default(0); // فقط للباقات
            $table->string('file_path')->nullable(); // مسار الملف إذا كان محتوى رقمي
            $table->timestamps();
            $table->index(['type', 'subject_id', 'education_stage_id', 'package_scope'], 'marketplace_items_composite_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketplace_items');
    }
};
