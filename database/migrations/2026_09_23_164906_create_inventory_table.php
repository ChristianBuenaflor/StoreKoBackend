<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('product');
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('profit', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('status')->default('In Stock');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};