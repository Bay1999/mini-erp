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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->string("sales_code");
            $table->string("item_code");
            $table->decimal("price", 10, 2)->default(0);
            $table->integer("qty")->default(0);
            $table->decimal("total", 10, 2)->default(0);
            $table->timestamps();

            $table->primary(["sales_code", "item_code"]);
            $table->foreign("sales_code")->references("sales_code")->on("sales_headers")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("item_code")->references("code")->on("items")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
