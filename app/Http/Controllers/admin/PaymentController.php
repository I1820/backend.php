<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Invoice;
use App\PaymentPortal;
use App\Repository\Helper\Response;
use App\Repository\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function list(Request $request)
    {
        $invoices = Invoice::skip(intval($request->get('offset')))
            ->take(intval($request->get('limit')) ?: 10)
            ->with('user')
            ->get();
        $overview = [
            'all_transactions' => $invoices->count(),
            'success_transactions' => $invoices->where('status', true)->count(),
            'failed_transactions' => $invoices->where('status', false)->count(),
            'total_income' => $invoices->sum('price')
        ];
        return Response::body(compact('overview', 'invoices'));
    }

    public function exportToExcel(Request $request)
    {
        $invoices = Invoice::skip(intval($request->get('offset')))
            ->take(intval($request->get('limit')) ?: 10)
            ->with('user')
            ->get();
        return $this->paymentService->toExcel($invoices);
    }

    public function overview()
    {
        $all_invoices = Invoice::get();
        $last_week_invoices = Invoice::where('created_at', Carbon::now()->subDays(7))->get();
        $overview = [
            'all_transactions_sum' => $all_invoices->sum('price'),
            'all_transactions_num' => $all_invoices->count(),
            'last_week_transactions_sum' => $last_week_invoices->sum('price'),
        ];
        return Response::body(compact('overview'));
    }
}
