<?php

namespace App\Exceptions;

use Exception;

class RenderException extends Exception
{
    public function report()
    {

    }

    public function render($request)
    {
            return response()->json(['mag' => '测试异常', 'code' => 403], 403);
    }
}
