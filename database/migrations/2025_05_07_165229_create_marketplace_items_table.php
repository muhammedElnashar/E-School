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
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['package', 'digital_asset']);
            $table->enum('package_scope', ['individual', 'group', 'both'])->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('lecture_credits')->default(0); // فقط للباقات
            $table->boolean('is_downloadable_content')->default(false);     // مضاف
            $table->string('file_path')->nullable(); // فقط للملفات
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
        Schema::dropIfExists('marketplace_items');
    }
};
