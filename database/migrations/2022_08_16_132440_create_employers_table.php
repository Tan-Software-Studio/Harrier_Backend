<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmployersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->nullable();
            $table->enum('is_pe', [0, 1])->default(0)->comment('0=no, 1=yes');
            $table->string('email')->unique()->comment('Super-User Email Address');
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('name')->nullable()->comment('Full Legal Name');
            $table->boolean('is_request')->default(1)->comment('0=no, 1=yes');
            $table->boolean('is_login')->default(0)->comment('0=no, 1=yes');
            $table->boolean('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->text('uk_address')->nullable();
            $table->text('hq_address')->nullable()->comment('if other than UK address');
            $table->text('billing_address')->nullable();
            $table->text('contact_details')->nullable()->comment('Point of Contact for Invoices (email address preferred)');
            $table->string('logo')->collation('utf8mb4_general_ci')->nullable();
            $table->string('password')->nullable();
            $table->string('url')->unique()->nullable()->comment('website url');
            $table->boolean('is_terms_and_conditions')->default(0)->comment('0=No, 1=Yes');
            $table->boolean('is_marketing_sign_up')->default(0)->comment('0=No, 1=Yes');
            $table->string('currency_code')->collation('utf8mb4_general_ci')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('employers');
    }
}
