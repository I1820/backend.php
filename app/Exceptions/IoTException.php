<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/12/17
 * Time: 9:18 AM
 */

namespace App\Exceptions;

use Exception;
use \Illuminate\Http\Request;
use \App\Repository\Helper\Response;

class IoTException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request)
    {
        return response()->json(Response::body($this->getMessage(), $this->getCode()), $this->getCode());
    }

}
