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
            // Add new 'name' column
            $table->string('name')->after('id');

            // Drop old columns
            $table->dropColumn(['first_name', 'last_name', 'phone_number', 'is_active']);

            // Modify role_id to be nullable and set onDelete behavior
            // First, drop the existing foreign key
            $table->dropForeign(['role_id']);

            // Then, change the column to be nullable
            $table->unsignedBigInteger('role_id')->nullable()->change();

            // Finally, add the foreign key back with the desired onDelete behavior
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('set null');
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
            $table->dropColumn('name');

            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone_number', 20)->nullable();
            $table->boolean('is_active')->default(true);

            $table->dropForeign(['role_id']);
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
        });
    }
};
