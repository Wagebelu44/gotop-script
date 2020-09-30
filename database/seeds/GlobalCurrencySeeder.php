<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GlobalCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_currencies')->insert([
			[
                'code' => 'USD',
				'sign' => '$',
				'name' => 'United States Dollars',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'RUB',
				'sign' => '₽',
				'name' => 'Russian Rubles',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'THB',
				'sign' => '฿',
				'name' => 'Thai Baht',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'TRY',
				'sign' => '₺',
				'name' => 'Turkish Lira',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'EUR',
				'sign' => '€',
				'name' => 'Euro',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'IDR',
				'sign' => 'Rp',
				'name' => 'Indonesian Rupiah',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'BRL',
				'sign' => 'R$',
				'name' => 'Brazilian Real',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'CNY',
				'sign' => '¥',
				'name' => 'Chinese Yuan',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'KRW',
				'sign' => '$',
				'name' => 'South Korean Won',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'INR',
				'sign' => '₹',
				'name' => 'Indian Rupee',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'IRR',
				'sign' => '﷼',
				'name' => 'Iranian Rial',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'SAR',
				'sign' => 'SR',
				'name' => 'Saudi Arabia Riyal',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'PLN',
				'sign' => '$',
				'name' => 'Polish złoty',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'MYR',
				'sign' => 'RM',
				'name' => 'Malaysian Ringgit',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'GBP',
				'sign' => '£',
				'name' => 'Pound sterling',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'KWD',
				'sign' => 'KD',
				'name' => 'Kuwaiti dinar',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'SEK',
				'sign' => 'kr',
				'name' => 'Swedish krona',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'ILS',
				'sign' => '₪',
				'name' => 'Israeli shekel',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'HKD',
				'sign' => '$',
				'name' => 'Hong Kong dollar',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'NGN',
				'sign' => '₦',
				'name' => 'Nigerian naira',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'KES',
				'sign' => '$',
				'name' => 'Kenyan shilling',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'JPY',
				'sign' => '¥',
				'name' => 'Japanese Yen',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'ARS',
				'sign' => '$',
				'name' => 'Argentine peso',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'VND',
				'sign' => '₫',
				'name' => 'Vietnamese đồng',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'CAD',
				'sign' => '$',
				'name' => 'Canadian dollar',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'IQD',
				'sign' => 'ع.د',
				'name' => 'Iraqi dinar',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'TWD',
				'sign' => '$',
				'name' => 'New Taiwan Dollars',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'AZN',
				'sign' => 'AZN',
				'name' => 'Azerbaijani manat',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'BYN',
				'sign' => 'BYN',
				'name' => 'Belarusian ruble',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'KZT',
				'sign' => '₸',
				'name' => 'Kazakhstani tenge',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'UAH',
				'sign' => '₴',
				'name' => 'Ukrainian hryvnia',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'RON',
				'sign' => 'lei',
				'name' => 'Romanian leu',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'AED',
				'sign' => 'د.إ',
				'name' => 'United Arab Emirates dirham',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'COP',
				'sign' => '$',
				'name' => 'Colombian peso',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'PKR',
				'sign' => '₨',
				'name' => 'Pakistan Rupee',
				'status' => 'Active',
				'created_at' => now(),
			],
            [
                'code' => 'EGP',
				'sign' => 'E£',
				'name' => 'Egyptian Pound',
				'status' => 'Active',
				'created_at' => now(),
			],

		]);
    }
}

