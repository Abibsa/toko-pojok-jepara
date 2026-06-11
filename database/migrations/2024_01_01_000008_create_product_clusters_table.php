<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_clusters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('cluster');
            $table->enum('priority_level', ['high', 'medium', 'low']);
            $table->decimal('frequency_score', 8, 4)->default(0);
            $table->decimal('quantity_score', 8, 4)->default(0);
            $table->decimal('urgency_score', 8, 4)->default(0);
            $table->timestamp('last_clustered_at')->nullable();
            $table->timestamps();
            
            $table->unique('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_clusters');
    }
};