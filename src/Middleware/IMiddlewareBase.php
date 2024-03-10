<?php

namespace Ainab\Really\Middleware;

use Ainab\Really\Model\Request;

interface IMiddlewareBase {
    public function handle(Request $request);
}
