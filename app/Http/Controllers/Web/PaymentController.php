<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Payment\WebmoneyController;
use App\Http\Controllers\Payment\PerfectMoneyController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ManualOrderPlaced;
use App\Models\SettingBonuse;
use App\Models\Transaction;
use App\User;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->payment_method == 1) {
            return (new PayPalController())->store($request);
        } elseif ($request->payment_method == 4) {
            return (new PerfectMoneyController())->store($request);
        } elseif ($request->payment_method == 5) {
            return (new WebmoneyController())->store($request);
        }

        return redirect()->back()->withError('No Payment method found to requested one');
    }
    
    public function transactionStore($amount, $paymentMethodId, $secret = null, $data = [])
    {
        $transaction = Transaction::create([
            'panel_id' => Auth::user()->panel_id,
            'user_id' =>  Auth::user()->id,
            'admin_id' => null,
            'global_payment_method_id' => $paymentMethodId ?? 0,
            'tnx_id' => $secret,
            'transaction_type' => 'deposit',
            'transaction_flag' => 'payment_gateway',
            'amount' => $amount,
            'memo' => isset($data['memo']) ? $data['memo'] : null,
            'fraud_risk' => isset($data['fraud_risk']) ? $data['fraud_risk'] : null,
            'transaction_detail' => json_encode(['payment_secrete'=>  $secret, 'currency_code'=> 'USD']),
            'payment_gateway_response' => null,
            'status' => 'hold',
        ]);

        if ($transaction) {

            $staffmails = staffEmails('payment_received', auth()->user()->panel_id);
            if (count($staffmails)>0) {
                $notification =  $notification = notification('Payment received', 2, auth()->user()->panel_id);
                if ($notification) {
                    if ($notification->status =='Active') {
                        Mail::to($staffmails)->send(new ManualOrderPlaced($transaction, $notification));
                    }
                }
            }

            Log::info("Transaction saved successfully. ID: ". $transaction->id);
            return $transaction;
        } else {
            Log::info("Transaction not saved. User ID: ". Auth::user()->id. ", Method ID: ".$paymentMethodId. ", Amount: ".$amount);
            return false;
        }
    }
    
    public function transactionPay($paymentMethodId, $tnxId, $data = [])
    {
        $transaction = Transaction::where('global_payment_method_id', $paymentMethodId)->where('tnx_id', $tnxId)->first();
        if (empty($transaction)) {
            Log::critical("Payment not found. details: TNX ID: ".$tnxId.", DATA: ".json_encode($data));
            return false;
        }

        $updateData = [
            'status' => $data['status'],
            'transaction_detail' => isset($data['detail']) ? $data['detail'] : null,
        ];
        if (isset($data['amount'])) {
            $updateData['amount'] = $data['amount'];
        }
        $transaction->update($updateData);

        DB::statement('UPDATE transactions t CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number FROM transactions) s SET t.sequence_number = s.new_sequence_number WHERE t.id='.$transaction->id);

        $bonusAmount = 0;
        $bonus = SettingBonuse::where('global_payment_method_id', $paymentMethodId)->where('panel_id', $transaction->panel_id)->first();
        if (!empty($bonus)) {
            if ( floatval($transaction->amount) >= floatval($bonus->deposit_from)) {
                $bonusAmount = ($bonus->bonus_amount / 100) * floatval($transaction->amount);
                $bonusTransaction = Transaction::create([
                    'panel_id' => $transaction->panel_id,
                    'user_id' =>  $transaction->user_id,
                    'admin_id' => null,
                    'global_payment_method_id' =>  $transaction->global_payment_method_id,
                    'tnx_id' => $transaction->tnx_id,
                    'transaction_type' => 'deposit',
                    'transaction_flag' => 'bonus_deposit',
                    'amount' =>  $bonusAmount,
                    'memo' => null,
                    'fraud_risk' => null,
                    'transaction_detail' => json_encode([
                        'payment_secrete'=>  $transaction->tnx_id,
                        'actual_amount'=> floatval($transaction->amount),
                        'currency_code'=> 'USD',
                    ]),
                    'payment_gateway_response' => null,
                    'status' => 'done',
                ]);

                if ($bonusTransaction) {
                    DB::statement('UPDATE transactions t CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number FROM transactions) s SET t.sequence_number = s.new_sequence_number WHERE t.id='.$bonusTransaction->id);
                } else {
                    Log::warning("Bonus not set. for this payment: ID: ".$transaction->id);
                }
            }
        }

        $user = User::find($transaction->user_id);
        $user->update(['balance' => ($user->balance+($transaction->amount+$bonusAmount))]);

        return true;
    }
}
