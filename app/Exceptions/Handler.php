<?php

namespace App\Exceptions;

use App\Repository\Helper\Response;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof IOTException) { # papad exceptions
            $response = $this->CustomException($exception);
        } else { # other exceptions
            $response = $this->otherExceptions($request, $exception);
        }
        return $response;
    }

    /**
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function CustomException(Exception $e)
    {
        $unauthorized = [
            AuthException::C_UA,
            AuthException::C_TE,
            AuthException::C_TI,
            AuthException::C_IC,
            AuthException::C_UNF,
        ];

        if (in_array($e->getCode(), $unauthorized))
            $code = 401;
        else
            $code = 200;
        return response(
            Response::body($e->getMessage(), $e->getCode()),
            $code
        );
    }

    /**
     * @param $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    private function otherExceptions($request, Exception $exception)
    {
        if (env('APP_DEBUG')) { # debug true
            $response = parent::render($request, $exception);
        } else { # debug false
            $response = response()->json(Response::body('server error', 500));
        }

        return $response;
    }
}
