<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 19/1/18
 * Time: 2:50 PM
 */

namespace App\Repository\Services\Payment;


use App\Invoice;
use App\Package;
use App\Repository\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zarinpal\Zarinpal;

class ZarinPalService
{
    protected $zarinpal;
    protected $userService;

    public function __construct(Zarinpal $zarinpal, UserService $userService)
    {
        $this->zarinpal = $zarinpal;
        $this->userService = $userService;

    }

    /**
     * @param Package $package
     * @param int $discount
     * @return Invoice|boolean
     */
    public function createInvoice(Package $package, int $discount)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        /**
         * @var Invoice
         */
        $invoice = Invoice::create([
            'user_id' => $user['_id'],
            'price' => ($package['price'] - $discount) >= 0 ? $package['price'] - $discount : 0,
            'discount' => $discount,
            'gate' => 'زرین پال',
            'package' => $package->toArray(),
            'status' => false,
        ]);
        $payment = [
            'CallbackURL' => route('payment.verify', $invoice['_id']),
            'Amount' => $invoice['price'] / 10, // convert rial to toman
            'Description' => 'پرداخت ' . $package['name'],
            'Email' => $user['email'],    // Optional
        ];
        if ($invoice['price'] == 0) {
            $invoice['status'] = true;
            $invoice->save();
            $this->userService->updatePackage($user, $invoice['package']);
            return $invoice;
        }

        $response = $this->zarinpal->request($payment);
        if ($response['Status'] === 100) {
            $authority = $response['Authority'];
            $invoice['authority'] = $authority;
            $invoice->save();
            return $invoice;
        }
        return false;
    }

    public function verify(Invoice $invoice, Request $request)
    {
        $payment = [
            'Authority' => $request->get('Authority'),
            'Status' => $request->get('Status'),
            'Amount' => $invoice['price'] / 10 // convert rial to toman
        ];
        $response = $this->zarinpal->verify($payment);
        if ($response['Status'] === 100) {
            $invoice->status = true;
            $invoice->save();
            return true;
        }
        return false;
    }

    public function pay(Invoice $invoice)
    {
        return redirect($this->zarinpal->getRedirectUrl($invoice['authority']));
    }

}
