<?php

namespace Digitalroll\Heimdal;

use Throwable;
use Illuminate\Http\JsonResponse;

class ResponseFactory
{
    public static function make(Throwable $e)
    {
        return new JsonResponse([
            'status' => 'error'
        ]);
    }
}
