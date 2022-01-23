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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends ApiClientTestCase
{
    /** @var string[] $credentials */
    protected static array $credentials;

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
    public function login(): void
    {
        /* Arrange */
        $endpoint = JwtDecorator::API_ENDPOINT;
        $method = JwtDecorator::API_ENDPOINT_METHOD;

        /* Act */
        $response = $this->doRequest($endpoint, $method);
        self::$credentials = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', self::$credentials);
    }

    /**
     * Test getting user without a token
     *
     * @test
     * @return void
     * @throws TransportExceptionInterface
     */
    public function withoutToken(): void
    {
        /* Arrange */
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, 1);
        $method = Request::METHOD_GET;

        /* Act */
        $this->doRequest($endpoint, $method);

        /* Assert */
        $this->assertResponseStatusCodeSame(401);
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
    public function withToken(): void
    {
        /* Arrange */
        $endpoint = $this->getApiEndpointItem(User::API_ENDPOINT_ITEM, 1);
        $method = Request::METHOD_GET;
        $expected = AppFixtures::getUserAsJson(1);

        /* Act */
        $response = $this->doRequest($endpoint, $method, ['auth_bearer' => self::$credentials['token']]);
        $current = $response->toArray();

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertSame($expected, $current);
    }
}
