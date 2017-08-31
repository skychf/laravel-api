<?php

namespace Skychf\Api\Format;

class Json
{
    private $message;

    private $data;

    private $meta;

    private $code = 200;

    private $errors;

    private $debug;

    public function setMessage($message = null)
    {
        $this->message = $message;
        return $this;
    }

    public function setData($data = null)
    {
        $this->data = $data;
        return $this;
    }

    public function setMeta($meta = null)
    {
        $this->meta = $meta;
        return $this;
    }

    public function setCode($code = 200)
    {
        $this->code = $code;
        return $this;
    }

    public function setErrors($errors = null)
    {
        $this->errors = $errors;
        return $this;
    }

    public function setDebug($debug = null)
    {
        $this->debug = $debug;
        return $this;
    }

    public function hasData()
    {
        return isset($this->data);
    }

    public function response()
    {
        $returnData = [];

        if ($this->message) {
            $returnData['message'] = $this->message;
        }

        if ($this->errors) {
            $returnData['errors'] = $this->errors;
        }

        if ($this->meta) {
            $returnData['meta'] = $this->meta;
        }

        if ($this->data) {
            $returnData['data'] = $this->data;
        }

        if ($this->debug) {
            $returnData['debug'] = $this->debug;
        }

        return response()->json(
            $returnData,
            $this->code,
            ['Content-Type' => 'application/json;charset=utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }
}