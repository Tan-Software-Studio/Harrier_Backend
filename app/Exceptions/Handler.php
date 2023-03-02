<?php

namespace App\Exceptions;

use BadMethodCallException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use ParseError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\Passport\Exceptions\MissingScopeException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            
        });

        $this->renderable(function (MissingScopeException $e, $request) {
            $bug = trans('msg.missing_scope_exception');
            return sendErrorHelper(trans('msg.error'), $bug, mScopeEx());
        });
        $this->renderable(function (BadMethodCallException $e, $request) {
            if ($request->is('api/*')) {
                $bug = $e->getMessage();
                return sendErrorHelper('BadMethodCallException', $bug, intSerError());
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return sendErrorHelper('Url not found.', [], unAuth());
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                $bug = $e->getMessage();
                return sendErrorHelper('The specified method for the request is invalid.', $bug, methNotAlwd());
            }
        });

        $this->renderable(function (ParseError $e, $request) {
            if ($request->is('api/*')) {
                $bug = $e->getMessage();
                return sendErrorHelper('ParseError', $bug, intSerError());
            }
        });

        $this->renderable(function (BindingResolutionException $e, $request) {
            $bug = $e->getMessage();
            return sendErrorHelper('BindingResolutionException', $bug, intSerError());
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        /** Unauthenticated */ 
        if (request()->expectsJson()) {
            return sendErrorHelper('Unauthenticated', [], 401);
        }
    
    }

    
}
