<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAdminIdLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::table('group_user', function (Blueprint $table) {
            if (Schema::hasColumn('group_user', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });

        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'admin_id')) {
                $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
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
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });
    }
}
