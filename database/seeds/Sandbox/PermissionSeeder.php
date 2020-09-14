<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrPermissions = [
            'user' => [
                'add user', 
                'edit user', 
                'change user status', 
                'edit user custom rates', 
                'export user', 
                'user sign in history'
            ],
            'order' => [
                'see order external id',
                'see order user',
                'see order charge',
                'see order cost',
                'view order',
                'resend order',
                'edit order link',
                'set start count order',
                'set remains order',
                'change order status',
                'set partial order',
                'cancel and refund order',
                'export order'
            ],
            'subscription' => [
                'see subscription external id',
                'view subscription',
                'edit subscription expiry',
                'change subscription status',
                'cancel subscription',
            ],
            'drip-feed' => [
                'change drip-feed status',
                'cancel and refund drip-feed',
            ],
            'task' => [
                'view task',
                'resend task',
                'change task status',
            ],
            'service' => [
                'see service provider',
                'import service',
                'add service',
                'add service subscription',
                'edit service',
                'edit service description',
                'change service status',
                'reset service custom rates',
                'delete service',
                'duplicate service',
                'add category',
                'edit category',
                'change category status'
            ],
            'payment' => [
                'add payment',
                'see payment',
                'view payment details',
                'edit payment',
                'report a fraud payment',
                'accept payment',
                'complete payment',
                'export payment'
            ],
            'ticket' => [
                'create ticket',
                'view ticket',
                'change ticket status',
                'close ticket',
                'close and lock ticket',
                'delete ticket',
                'mark as unread ticket',
                'submit ticket message',
                'edit ticket message',
                'delete ticket message'
            ],
            'affiliate' => [
                'see affiliate',
                'change affiliate status',
                'see affiliate referrals',
                'see affiliate payouts',
                'approve or reject affiliate payout'
            ],
            'report' => [
                'payment report',
                'order report',
                'ticket report',
                'profit report'
            ],
            'appearance' => [
                'pages',
                'blog',
                'menu',
                'themes',
                'languages',
                'files'
            ],
            'setting' => [
                'general setting',
                'provider setting',
                'payment setting',
                'module setting',
                'integration setting',
                'notification setting',
                'bonus setting',
                'faq setting',
            ]
        ];

        foreach($arrPermissions as $key => $apArr)
        {
            foreach($apArr as $ap)
            {
                Permission::create(['module' => $key, 'name' => $ap, 'guard_name' => 'admin']);
            }
        }
    }
}
