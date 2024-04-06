<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestHandler
{
    public static function getRequestParameter(Request $request, string $parameter, bool $required = true): mixed
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            if ($required && !$request->query->has($parameter)) throw new BadRequestHttpException("$parameter is required");
            return $request->query->get($parameter);
        } else {
            $body = $request->toArray();
            if ($required && !key_exists($parameter, $body)) throw new BadRequestHttpException("$parameter is required");
            return key_exists($parameter, $body) ? $body[$parameter] : null;
        }
    }
}
