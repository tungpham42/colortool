<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex');
            $table->string('rgb')->nullable();
            $table->string('hsl')->nullable();
            $table->string('cmyk')->nullable();
            $table->string('pantone')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('color_palettes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('colors'); // JSON array of color IDs or hex codes
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('extracted_colors', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->text('colors'); // JSON array of extracted colors
            $table->integer('color_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracted_colors');
        Schema::dropIfExists('color_palettes');
        Schema::dropIfExists('colors');
    }
};
