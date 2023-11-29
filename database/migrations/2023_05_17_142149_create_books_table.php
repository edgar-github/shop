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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title_hy')->unique()->nullable();
            $table->string('title_en')->unique()->nullable();
            $table->text('text_hy', 255)->nullable();
            $table->text('text_en', 255)->nullable();
            $table->text('description_hy')->nullable();
            $table->text('description_en')->nullable();
            $table->string('book_size_hy', 60)->nullable();
            $table->string('book_size_en', 60)->nullable();
            $table->string('main_image', 255);
            $table->text('video_url')->nullable();
            $table->string('slug', 255)->unique();
            $table->string('price');
            $table->string('word_count')->nullable();
            $table->string('page_count');
            $table->string('font_size')->nullable();
            $table->string('isbn');
            $table->integer('in_stock');
            $table->string('published_date');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
