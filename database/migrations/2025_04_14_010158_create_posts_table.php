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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('featured_image')->nullable();
            $table->string('excerpt')->nullable();
            $table->longText('content');
            $table->string('slug')->unique();
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['published', 'draft', 'archived'])
                ->default('published');
            $table->json('tags')->nullable();
            $table->dateTime('published_at')->nullable()->default(now());
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
