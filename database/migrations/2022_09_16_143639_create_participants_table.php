<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->foreignId("conversation_id")
                ->constrained("conversations")
                ->cascadeOnDelete();
            $table->foreignId("user_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->enum("role",["admin","member"])->defualt("member");
            $table->timestamp("joined_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
};
