<?php

namespace App\Exceptions;

use App\Repository\Helper\Response;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     *
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceOf ValidationException) {
            $errs = $exception->errors(); // gets all validation errors
            return response(Response::body($errs[array_key_first($errs)][0], 400), 400); // return the first validation error to the user
        }

        return parent::render($request, $exception);
    }
}
