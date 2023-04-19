<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    public function __construct()
    {
        Schema::disableForeignKeyConstraints();
    }
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('wallet_user')->nullable()->unique()->index();
            $table->string('email')->unique()->index();
            $table->string('phone')->unique()->index();
            $table->string('password');
            $table->string("user_type")->index();
            $table->boolean("is_blocked")->default(false);
            $table->boolean("allow_login")->default(false);

            $table->boolean("is_password_changed")->default(false);


            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
