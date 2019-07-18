<?php

namespace App\Http\Controllers;

use App\Repository\Helper\Response;

class AboutController extends Controller
{
    public function __invoke()
    {
        return Response::body('18.20 is leaving us');
    }
}
