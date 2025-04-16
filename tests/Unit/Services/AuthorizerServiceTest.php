<?php

namespace Tests\Unit\Services;

use App\Exceptions\Business\ExternalAuthorizationFailedException;
use App\Exceptions\Business\ExternalAuthorizationNotAllowedException;
use App\Repositories\External\AuthorizerRepository;
use App\Services\AuthorizerService;
use Illuminate\Http\Client\ConnectionException;
use Tests\TestCase;

class AuthorizerServiceTest extends TestCase
{
    private AuthorizerService $service;
    private AuthorizerRepository $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(AuthorizerRepository::class);
        $this->service = new AuthorizerService($this->repositoryMock);
    }

    public function testAuthorizeSuccessfully()
    {
        $this->repositoryMock->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $result = $this->service->authorize();

        $this->assertTrue($result);
    }

    public function testAuthorizeThrowsExternalAuthorizationFailedException()
    {
        $this->expectException(ExternalAuthorizationFailedException::class);

        $this->repositoryMock->expects($this->once())
            ->method('authorize')
            ->willThrowException(new ExternalAuthorizationFailedException());

        $this->service->authorize();
    }

    public function testAuthorizeThrowsExternalAuthorizationNotAllowedException()
    {
        $this->expectException(ExternalAuthorizationNotAllowedException::class);

        $this->repositoryMock->expects($this->once())
            ->method('authorize')
            ->willThrowException(new ExternalAuthorizationNotAllowedException());

        $this->service->authorize();
    }

    public function testAuthorizeThrowsConnectionException()
    {
        $this->expectException(ConnectionException::class);

        $this->repositoryMock->expects($this->once())
            ->method('authorize')
            ->willThrowException(new ConnectionException());

        $this->service->authorize();
    }
}
