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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_generated_at')) {
                $table->dateTime('otp_generated_at')
                  ->nullable()->comment('used to verify the time for expire')->after('otp');
            }
            if (!Schema::hasColumn('users', 'token_response')) {
                $table->longText('token_response')
                  ->nullable()->comment('used to retrive the token after otp verified')->after('otp_generated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'token_response')) {
                $table->dropColumn('token_response');
            }
            if (Schema::hasColumn('users', 'otp_generated_at')) {
                $table->dropColumn('otp_generated_at');
            }
        });
    }
};
