<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('role_id')->unsigned()->default(2)->index('users_role_id_foreign');
			$table->string('name', 191)->nullable();
			$table->string('slug', 191)->nullable();
			$table->string('email', 191)->unique();
			$table->string('address', 255)->nullable();
			$table->string('secondary_email', 191)->nullable();
			$table->string('password', 191);
			$table->text('image', 65535)->nullable();
			$table->string('verify_token', 191)->nullable();
			$table->string('forgot_password_token', 191)->nullable();
			$table->text('auth_token', 65535)->nullable();
			$table->boolean('status')->default(0)->comment('0: unverified , 1 : verified , 2 deactivated ..');
			$table->string('remember_token', 100)->nullable();
			$table->boolean('email_notification')->default(0);
			$table->boolean('push_notification')->default(1);
			$table->string('lat', 50);
			$table->string('lng', 50);
			$table->string('device_type', 50)->comment('IOS , ANDROID');
			$table->text('device_id', 65535);
			$table->text('social_id', 65535);
			$table->string('signup_type', 50)->default('app')->comment('app,facebook,gmail');
			$table->bigInteger('current_login');
			$table->bigInteger('last_login');
			$table->bigInteger('created_at');
			$table->boolean('is_multirole')->default(0)->comment('0: no , 1 : yes');
			$table->bigInteger('updated_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
