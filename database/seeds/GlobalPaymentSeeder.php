<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                'uuid' => Str::uuid(),
				'name' => 'PayPal',
				'fields' => '{"PAYPAL_EMAIL":"PayPal Email Address"}',
				'status' => '1',
				'created_at' => now(),
			],
			[
                'uuid' => Str::uuid(),
				'name' => 'PayOP',
				'fields' => '{"PAYOP_SECRET_KEY":"Secret Key", "PAYOP_PUBLIC_KEY":"Public Key", "PAYOP_JWT_TOKEN": "JWT token"}',
				'status' => '1',
				'created_at' => now(),
			],
			[
                'uuid' => Str::uuid(),
				'name' => 'Coinbase',
				'fields' => '{"MERCHANT_ID":"Merchant ID", "COINBASE_SECRET_KEY":"Secret Key"}',
				'status' => '1',
				'created_at' => now(),
			],
			[
                'uuid' => Str::uuid(),
				'name' => 'PerfectMoney',
				'fields' => '{"PAYEE_ACCOUNT":"  i.e:  U25983854", "PAYEE_NAME":" I.e : shop name (optional)"}',
				'status' => '1',
				'created_at' => now(),
			],
			[
                'uuid' => Str::uuid(),
				'name' => 'WebMoney',
				'fields' => '{"LMI_PAYEE_PURSE":"  i.e:  Z297212868786", "LMI_PAYMENT_DESC":" I.e : shop name (optional)"}',
				'status' => '1',
				'created_at' => now(),
			],
		]);
    }
}
