<?php

use App\Enum\StatusEnum;
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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('url')->unique();
            $table->string('thumbnail')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['published', 'draft', 'archived'])
                ->default('published');
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->string('video_id')->unique();
            $table->string('video_type')->default('youtube');
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
