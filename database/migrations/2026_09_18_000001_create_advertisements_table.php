<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_type')->default('text')->comment('video|image|youtube|text');
            $table->string('media_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->unsignedInteger('duration_secs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_live')->default(false)->comment('Currently displayed ad in single mode');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
