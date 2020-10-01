<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            [
                'name' => 'Account',
                'url' => 'account',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Add Funds',
                'url' => 'add-funds',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Affiliates',
                'url' => 'affiliates',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'API',
                'url' => 'api',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Blog',
                'url' => 'blog',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
             [
                 'name' => 'Child Panels',
                 'url' => 'child-panels',
                 'content' => '',
                 'meta_title' => '',
                 'meta_keyword' => '',
                 'meta_description' => '',
                 'is_public' => 'No',
                 'is_editable' => 'Yes',
                 'status' => 'Active',
             ],
            [
                'name' => 'Drip Feed',
                'url' => 'drip-feed',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Mass Order',
                'url' => 'mass-order',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'New Order',
                'url' => 'new-order',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Orders',
                'url' => 'orders',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'FAQ',
                'url' => 'faq',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Services',
                'url' => 'services',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Sign in',
                'url' => 'sign-in',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Sign up',
                'url' => 'sign-up',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Subscriptions',
                'url' => 'subscriptions',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Terms',
                'url' => 'terms',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'Yes',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
            [
                'name' => 'Tickets',
                'url' => 'tickets',
                'content' => '',
                'meta_title' => '',
                'meta_keyword' => '',
                'meta_description' => '',
                'is_public' => 'No',
                'is_editable' => 'Yes',
                'status' => 'Active',
            ],
        ];

        DB::table('global_pages')->insert($pages);
    }
}
