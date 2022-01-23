<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Tests\Api;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\OpenApi\JwtDecorator;
use App\Tests\TestCase\ApiClientTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends ApiClientTestCase
{
    /** @var string[] $credentialsUser1 */
    protected static array $credentialsUser1;

    /** @var string[] $credentialsUser2 */
    protected static array $credentialsUser2;

    /**
     * This method is called before class.
     *
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::initClientEnvironment();
    }

    /**
     * Test wrong login.
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function wrongLoginUser1(): void
    {
        /* Arrange */
        $endpoint = JwtDecorator::API_ENDPOINT;
        $method = JwtDecorator::API_ENDPOINT_METHOD;
        $userId = 1;
        $options = [
            'json' => [
                'email' => AppFixtures::getEmail($userId),
                'password' => 'wrong-password',
            ]
        ];

        /* Act */
        $this->doRequest($endpoint, $method, $options);

        /* Assert */
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test login.
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function loginUser1(): void
    {
        /* Arrange */
        $endpoint = JwtDecorator::API_ENDPOINT;
        $method = JwtDecorator::API_ENDPOINT_METHOD;
        $userId = 1;
        $options = [
            'json' => [
                'email' => AppFixtures::getEmail($userId),
                'password' => AppFixtures::getPassword($userId),
            ]
        ];

        /* Act */
        $response = $this->doRequest($endpoint, $method, $options);
        self::$credentialsUser1 = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', self::$credentialsUser1);
    }

    /**
     * Test getting user without a token
     *
     * @test
     * @return void
     * @throws TransportExceptionInterface
     */
    public function withoutTokenUser1(): void
    {
        /* Arrange */
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, 1);
        $method = Request::METHOD_GET;

        /* Act */
        $this->doRequest($endpoint, $method);

        /* Assert */
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test getting user with a token
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function withTokenUser1(): void
    {
        /* Arrange */
        $userId = 1;
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, $userId);
        $method = Request::METHOD_GET;
        $expected = AppFixtures::getUserAsJson($userId);

        /* Act */
        $response = $this->doRequest($endpoint, $method, bearer: self::$credentialsUser1['token']);
        $current = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertSame($expected, $current);
    }

    /**
     * Test getting forbidden user.
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function tryForbiddenUser1(): void
    {
        /* Arrange */
        $userId = 2;
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, $userId);
        $method = Request::METHOD_GET;

        /* Act */
        $this->doRequest($endpoint, $method, bearer: self::$credentialsUser1['token']);

        /* Assert */
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test login.
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function loginUser2(): void
    {
        /* Arrange */
        $endpoint = JwtDecorator::API_ENDPOINT;
        $method = JwtDecorator::API_ENDPOINT_METHOD;
        $userId = 2;
        $options = [
            'json' => [
                'email' => AppFixtures::getEmail($userId),
                'password' => AppFixtures::getPassword($userId),
            ]
        ];

        /* Act */
        $response = $this->doRequest($endpoint, $method, $options);
        self::$credentialsUser2 = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', self::$credentialsUser2);
    }

    /**
     * Test getting user with a token
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function withTokenUser2(): void
    {
        /* Arrange */
        $userId = 2;
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, $userId);
        $method = Request::METHOD_GET;
        $expected = AppFixtures::getUserAsJson($userId);

        /* Act */
        $response = $this->doRequest($endpoint, $method, bearer: self::$credentialsUser2['token']);
        $current = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertSame($expected, $current);
    }

    /**
     * Test getting forbidden user.
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function tryForbiddenUser2(): void
    {
        /* Arrange */
        $userId = 1;
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, $userId);
        $method = Request::METHOD_GET;

        /* Act */
        $this->doRequest($endpoint, $method, bearer: self::$credentialsUser2['token']);

        /* Assert */
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
