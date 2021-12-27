<?php

namespace App\Exceptions;

use Log;
use Throwable;
use App\Support\LaravelLoggerUtil;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if($request->ajax()) {
            return $this->ajaxRender($request, $exception);
        }

        return parent::render($request, $exception);
    }

    private function ajaxRender($request, Throwable $exception)
    {
        // Validation
        if ($exception instanceof ValidationException) {
            $validationErrors = [];
            foreach ($exception->errors() as $key => $item) {
                $validationErrors[$key] = $item[0];
            }

            return $this->exceptionResponse(
                $exception, 
                Response::HTTP_UNPROCESSABLE_ENTITY, 
                'Validation Error', 
                $validationErrors
            );
        }

        // Other issues
        return $this->exceptionResponse(
            $exception, 
            Response::HTTP_INTERNAL_SERVER_ERROR, 
            'Server Error',
            ['Server Error']
        );
    }

    // 統一回覆格式
    private function exceptionResponse(Throwable $e, int $code, string $message, array $errors)
    {
        if($code === Response::HTTP_INTERNAL_SERVER_ERROR) {
            LaravelLoggerUtil::loggerException($e);
        }

        $data = [
            'code'    => $code,
            'message' => $message,
            'errors' => $errors,
        ];

        if (!app()->isProduction()) {
            $data['debug'] = [
                'message' => $e->getMessage(),
                'trace'   => $e->getTrace(),
            ];
        }

        return response()->json($data, $code);
    }
}