<?php

namespace ZaLaravel\LaravelPayeer\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Payment;
use Illuminate\Http\Request;
use DB;

/**
 * Class PaymentPayeerController
 * @package ZaLaravel\LaravelPayeer\Controllers
 */
class PaymentPayeerController extends Controller{

    public function createPayment(Request $request)
    {
        \Auth::user();
        $user = $request->user()->id;
        $m_shop =  '75722594';
        $m_orderid = mt_rand();
        $m_amount = number_format($request->get('OutSum'), 2, '.', '');
        $m_curr = 'RUB';
        $m_desc = base64_encode('Пополнение баланса');
        $m_key = 'halyava';

        $arHash = array(
            $m_shop,
            $m_orderid,
            $m_amount,
            $m_curr,
            $m_desc,
            $m_key
        );

        $sign = strtoupper(hash('sha256', implode(':', $arHash)));

        //dd($sign);
        if($m_amount != 0) {
            try {
                DB::beginTransaction();
                    $payment = new Payment();
                    $payment->uid = $m_orderid;
                    $payment->user_id = $user;
                    $payment->balance = $m_amount;
                    $payment->description = base64_decode($m_desc);
                    $payment->operation = '+';
                    $payment->save();
                DB::commit();
            } catch (\PDOException $e){

                print $e->getMessage();
                DB::connection()->getPdo()->rollBack();
            }
        }
         /*return redirect()->action('ZaLaravel\LaravelPayeer\Controllers\IpnPayeerController@getResult',
             array('m_shop' => $m_shop,
                 'm_amount' => $m_amount,
                 'm_orderid' => $m_orderid,
                 'm_curr' => $m_curr,
                 'm_desc' => $m_desc,
                 'm_sign' => $sign));*/
        header("Location:https://payeer.com/merchant/?m_shop=$m_shop&m_orderid=$m_orderid&m_amount=$m_amount&m_curr=$m_curr&m_desc=$m_desc&m_sign=$sign");
    }
}