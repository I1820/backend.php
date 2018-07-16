<?php

namespace App\Http\Controllers\admin;

use App\Invoice;
use App\Repository\Helper\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
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

    public function overview()
    {
        $all_invoices = Invoice::get();
        $last_week_invoices = Invoice::where('created_at', Carbon::now()->subDays(7))->get();
        $overview = [
            'all_transactions_sum' => $all_invoices->sum('price'),
            'last_week_transactions_sum' => $last_week_invoices->sum('price'),
        ];
        return Response::body(compact('overview'));
    }
}
