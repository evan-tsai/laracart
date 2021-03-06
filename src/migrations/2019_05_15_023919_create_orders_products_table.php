<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');

            $table->foreign('order_id')->references('id')->on(config('laracart.tables.order'))->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on(config('laracart.tables.product'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->getTableName());
    }

    protected function getTableName()
    {
        return config('laracart.tables.order') . '_' . config('laracart.tables.product');
    }
}
