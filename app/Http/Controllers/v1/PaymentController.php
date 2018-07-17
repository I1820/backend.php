<?php

namespace App\Http\Controllers\v1;

use App\Discount;
use App\Exceptions\GeneralException;
use App\Invoice;
use App\Package;
use App\Repository\Helper\Response;
use App\Repository\Services\Payment\ZarinPalService;
use App\Repository\Services\UserService;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $zarinPalService;
    protected $userService;

    public function __construct(ZarinPalService $zarinPalService,
                                UserService $userService)
    {
        $this->zarinPalService = $zarinPalService;
        $this->userService = $userService;
    }

    public function createInvoice(Package $package, Request $request)
    {
        $code = $request->get('code');
        $discount = $this->discount($code);
        $invoice = $this->zarinPalService->createInvoice($package, $discount);
        if ($invoice)
            return Response::body(compact('invoice'));
        throw new GeneralException(GeneralException::M_UNKNOWN, GeneralException::UNKNOWN_ERROR);

    }

    public function pay(Invoice $invoice)
    {
        return $this->zarinPalService->pay($invoice);
    }

    private function discount($code)
    {
        if (!$code)
            return 0;
        $discount = Discount::where('code', $code)->first();
        if (!$discount)
            throw new GeneralException('کد تخفیف اشتباه است', GeneralException::NOT_FOUND);
        if ($discount['expired'])
            throw new GeneralException('کد تخفیف استفاده شده است', GeneralException::NOT_FOUND);
        $discount->expired = true;
        $discount->save();
        return $discount->value;
    }

    public function callback(Invoice $invoice, Request $request)
    {
        if ($this->zarinPalService->verify($invoice, $request)) {
            $uri = 'payment/success?price=' . $invoice['price'] . '&authority=' . $invoice['authority'];
            $this->userService->updatePackage($invoice->user()->first(), $invoice['package']);
            return redirect(env('FRONT_URL') . $uri);
        }
        return redirect(env('FRONT_URL') . 'payment/failure');
    }

    public function list(Request $request)
    {
        $invoices = Auth::user()->invoices();
        if (intval($request->get('offset')))
            $invoices = $invoices->skip(intval($request->get('offset')));
        if (intval($request->get('limit')))
            $invoices = $invoices->take(intval($request->get('limit')) ?: 10);
        $invoices = $invoices->get();
        return Response::body(compact('invoices'));
    }
}
