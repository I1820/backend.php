<?php

namespace App\Http\Controllers\admin;

use App\Invoice;
use App\Repository\Helper\Response;
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
        return Response::body(compact('invoices'));
    }
}
