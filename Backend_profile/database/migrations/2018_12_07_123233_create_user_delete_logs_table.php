<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDeleteLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_delete_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('role_id');
			$table->string('email');
			$table->integer('status');
			$table->bigInteger('created_at');
			$table->bigInteger('updated_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_delete_logs');
	}

}
