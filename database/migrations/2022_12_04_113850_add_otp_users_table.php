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
            if (!Schema::hasColumn('users', 'otp'))
            {
                $table->integer('otp')->length(6)->nullable()->after('password')->comment('6 Digit one time password');
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

            Schema::table('users', function (Blueprint $table)
            {
                if (Schema::hasColumn('users', 'otp'))
                {
                    $table->dropColumn('otp');
                }
            });

    }
};
