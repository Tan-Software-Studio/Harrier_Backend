<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', [roleAdmin(), roleGuest(), roleEmp()])->default(roleGuest());
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_request')->default(1)->comment('1=requested, 2=expired, 3=active');
            $table->boolean('is_login')->default(0)->comment('0=no, 1=yes');
            $table->boolean('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->string('password');
            $table->rememberToken();
            $table->integer('expire_datetime')->nullable();
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
        Schema::dropIfExists('users');
    }
}
