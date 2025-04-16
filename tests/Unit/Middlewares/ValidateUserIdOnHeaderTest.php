<?php

namespace Tests\Unit\Middlewares;

use App\Exceptions\Business\BusinessException;
use App\Exceptions\Business\UserIdNotProvidedException;
use App\Http\Middlewares\ValidateUserIdOnHeader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ValidateUserIdOnHeaderTest extends TestCase
{
    private ValidateUserIdOnHeader $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ValidateUserIdOnHeader();
    }

    public function testHandleWithValidUserId()
    {
        $request = new Request();
        $request->headers->set('user-id', '123');

        $next = function ($req) {
            return response('OK', Response::HTTP_OK);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function testHandleThrowsExceptionWhenUserIdMissing()
    {
        $request = new Request();

        $next = function ($req) {
            return response('user-id must be provided on header', Response::HTTP_BAD_REQUEST);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
