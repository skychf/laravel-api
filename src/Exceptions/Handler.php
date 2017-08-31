<?php

namespace Skychf\Api\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler
{
    private $code = 200;

    private $message;

    private $errors;

    private $debug;

    public function render($request, $e, $format)
    {
        switch ($e) {
            case $e instanceof ValidationException:
                $this->code    = 422;
                $this->message = '参数验证失败';
                $this->errors  = $e->validator->errors()->getMessages();
                break;
            case $e instanceof ModelNotFoundException:
                $this->code    = 404;
                $this->message = '请求的数据没有找到';
                break;
            case $e instanceof NotFoundHttpException:
                $this->code    = 404;
                $this->message = '你访问的地扯不存在';
                break;
            case $e instanceof AuthenticationException:
                $this->code    = 401;
                $this->message = '没有登录';
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $this->code    = 405;
                $this->message = '方法不被允许';
            default:
                $this->code    = 500;
                $this->message = '服务器内部错误！';
                break;
        }

        if ($request->header('debug')) {
            $this->debug = $e->getTrace();
            $other = [
                'url' => $request->fullUrl(),
                'args' => $request->all(),
            ];
            array_push($this->debug, $other);
        }

        if (! $this->errors) $this->errors = $e->getMessage();

        return $format->setMessage($this->message)->setCode($this->code)->setErrors($this->errors)->setDebug($this->debug)->response();
    }
}
