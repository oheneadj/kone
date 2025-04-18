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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->constrained('providers')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('type_id')
                ->constrained('types');
            $table->longText('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('product_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
