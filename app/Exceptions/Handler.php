<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($this->isDatabaseConnectionFailure($e) && $request instanceof Request && ! $request->expectsJson()) {
            return response()->view('errors.database', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $e);
    }

    /**
     * Determine if the given exception represents a database connectivity failure.
     */
    protected function isDatabaseConnectionFailure(Throwable $e): bool
    {
        if ($e instanceof PDOException) {
            return true;
        }

        return $e instanceof QueryException && str_contains(strtolower($e->getMessage()), 'connection');
    }
}
