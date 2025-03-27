<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTypeEnumInExpenseParticipations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_participations', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('expense_participations', function (Blueprint $table) {
            $table->enum('type', ['payeur', 'benificier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_participations', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('expense_participations', function (Blueprint $table) {
            $table->enum('type', ['payeur,benificier']);
        });
    }
}
