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
            $table->uuid('id')->primary();
            $table->string('sku')->unique;
            $table->string('title')->unique;
            $table->string('slug');
            $table->decimal('price',28,8)->default(0);
            $table->text('description');
            $table->enum('status',['inactive','active','draft'])->default('draft');
            $table->string('image');
            $table->string('inventory_id')->default(0);
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
