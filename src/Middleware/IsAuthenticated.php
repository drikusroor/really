<?php

namespace Ainab\Really\Middleware;
use Ainab\Really\Service\JwtService;
use Ainab\Really\Middleware\IMiddlewareBase;
use Ainab\Really\Model\Request;

class IsAuthenticated implements IMiddlewareBase {

    public function __construct(private JwtService $jwtService) {
    }

    public function handle(Request $request) {
        $headerToken = $request->header('Authorization');

        if (!$headerToken) {
            $cookieToken = $request->cookie('jwt'); 
        }

        $token = $headerToken ?? $cookieToken;

        if (!$token) {
            header('Location: /auth/login');
        }

        $authenticated = $this->jwtService->validateToken($token);

        if (!$authenticated) {
            header('Location: /auth/login');
        }

        return $request;
    }
}

