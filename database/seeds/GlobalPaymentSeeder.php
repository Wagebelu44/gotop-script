<?php

use Illuminate\Database\Seeder;

class GlobalPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_payment_methods')->insert([
			[
			'name' => 'PayPal',
			'fields' => '{"PAYPAL_EMAIL":"PayPal Email Address"}',
			'status' => '1',
			'created_at' => now(),
			],
			[
			'name' => 'PayOP',
			'fields' => '{"PAYOP_SECRET_KEY":"Secret Key", "PAYOP_PUBLIC_KEY":"Public Key"}',
			'status' => '1',
			'created_at' => now(),
			],
			[
			'name' => 'Coinbase',
			'fields' => '{"MERCHANT_ID":"Merchant ID", "COINBASE_SECRET_KEY":"Secret Key"}',
			'status' => '1',
			'created_at' => now(),
			],
		]);
    }
}
