<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('external_id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('image')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'external_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
