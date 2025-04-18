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
        Schema::create('affiliate_link_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_link_id')->constrained('affiliate_links');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_id')->nullable();
            $table->string('utm_adgroup')->nullable();
            $table->string('utm_creative')->nullable();
            $table->string('utm_matchtype')->nullable();
            $table->string('utm_network')->nullable();
            $table->string('utm_device')->nullable();
            $table->string('utm_placement')->nullable();
            $table->string('utm_adposition')->nullable();
            $table->string('utm_target')->nullable();
            $table->string('utm_adid')->nullable();
            $table->string('utm_adgroupid')->nullable();
            $table->string('utm_campaignid')->nullable();
            $table->string('utm_adsetid')->nullable();
            $table->string('utm_adset')->nullable();
            $table->string('utm_ad')->nullable();
            $table->string('utm_source_id')->nullable();
            $table->string('utm_source_name')->nullable();
            $table->string('utm_source_type')->nullable();
            $table->string('utm_source_subtype')->nullable();
            $table->string('utm_source_subtype_id')->nullable();
            $table->string('utm_source_subtype_name')->nullable();
            $table->string('utm_source_subtype_type')->nullable();
            $table->string('utm_source_subtype_type_id')->nullable();
            $table->string('utm_source_subtype_type_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_link_data');
    }
};
