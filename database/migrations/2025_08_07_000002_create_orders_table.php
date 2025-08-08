<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('external_id');
            $table->string('customer_name')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'external_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
