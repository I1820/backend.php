<?php

namespace App\Http\Controllers\admin;

use App\Log;
use App\Repository\Helper\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function logs(Request $request)
    {
        try {
            $data = ['sorted' => json_decode($request->get('sorted'), true) ?: [], 'filtered' => json_decode($request->get('filtered'), true) ?: []];
        } catch (\Error $e) {
            $data = ['sorted' => [], 'filtered' => []];
        }
        if (count($data['sorted']))
            $logs = Log::orderBy($data['sorted'][0]['id'], $data['sorted'][0]['desc'] ? 'DESC' : 'ASC');
        else
            $logs = Log::orderBy('created_at');

        foreach ($data['filtered'] as $item)
            $logs->where($item['id'], 'like', '%' . $item['value'] . '%');

        $pages = ceil($logs->count() / (intval($request->get('limit')) ?: 10));
        $logs = $logs->skip(intval($request->get('offset')))->take(intval($request->get('limit')) ?: 10)->get();

        return Response::body(['logs' => $logs, 'pages' => $pages]);
    }
}
