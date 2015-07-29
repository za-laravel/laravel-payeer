<?php

namespace ZaLaravel\LaravelPayeer\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\User;
use DB;

/**
 * Class IpnPayeerController
 * @package ZaLaravel\LaravelPayeer\Controllers
 */
class IpnPayeerController extends Controller{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function getResult(Request $request)
    {
        if ($_SERVER['REMOTE_ADDR'] != '37.59.221.230') return;

        //dd(\Request::all());
        $m_key = 'halyava';
        $m_shop = $request->get('m_shop');
        $m_orderid = $request->get('m_orderid');
        $m_amount = $request->get('m_amount');
        $m_curr = $request->get('m_curr');
        $m_desc = $request->get('m_desc');
        $checksum = $request->get('m_sign');
        $user = Payment::select('user_id')->where('uid', '=', $m_orderid)->first();

        if (isset($_POST['m_operation_id']) && isset($checksum)) {

            $arHash = array($m_shop, $m_orderid, $m_amount, $m_curr, $m_desc, $m_key);
            $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));

            if ($checksum == $sign_hash && $_POST['m_status'] == 'success') {
                if (Payment::where('uid', '=', $m_orderid) && Payment::where('balance', '=', $m_amount)) {
                    try {
                        DB::beginTransaction();
                        $payment = Payment::where('uid', '=', $m_orderid)->first();
                        if ($payment->status == 0) {
                            $payment->status = 1;
                            $payment->update();
                            $addBalanceToUser = User::find($user->user_id);
                            $addBalanceToUser->balance += $m_amount;
                            $addBalanceToUser->update();
                        }
                        DB::commit();
                    } catch (\PDOException $e) {

                        \Session::flash('message', "$e->getMessage()");
                        DB::connection()->getPdo()->rollBack();
                    }
                }
            }
        }
        return redirect()->action('ProfileController@index');
    }
}