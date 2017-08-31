<?php

namespace Skychf\Api;

use Closure;
use Skychf\Api\Format\Json;
use Skychf\Api\Exceptions\Handler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Dispatcher
{
    protected $format;

    protected $handler;

    protected static $instance;

    public function __construct($format = 'json')
    {
        if ($format == 'json') {
            $this->format = new Json;
        }
        $this->handler = new Handler;
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function response(...$args)
    {
        foreach ($args as $arg) {
            if ($arg instanceof Closure) {
                call_user_func($callback, $this->format);
            } elseif ($arg instanceof LengthAwarePaginator) {
                $this->format->setData($arg->items());
                $this->format->setMeta([
                    'current_page' => $arg->currentPage(),
                    'from'         => $arg->firstItem(),
                    'last_page'    => $arg->lastPage(),
                    'per_page'     => $arg->perPage(),
                    'to'           => $arg->lastItem(),
                    'total'        => $arg->total()
                ]);
            } elseif (is_numeric($arg)) {
                $this->format->setCode($arg);
            } elseif (is_string($arg)) {
                $this->format->setMessage($arg);
            } elseif (is_array($arg)) {
                if (! $this->format->hasData()) {
                    $this->format->setData($arg);
                } else {
                    $this->format->setMeta($arg);
                }
            }
        }

        return $this->format->response();
    }

    public function exception($request, $e)
    {
        return $this->handler->render($request, $e, $this->format);
    }
}