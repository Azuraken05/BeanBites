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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')constrained('users_table', 'user_id')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('caterogy_id')->constrained('categories')->onDelete('set null');;
            $table->double('price');
            $table->string('image_url')->nullable();
            $table->integer('stock');
            $table->boolean('is_active')->default(true);
            $table->timestamps('created_at')->nullable();
         });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
