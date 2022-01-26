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

use App\Controller\VersionController;
use App\Tests\TestCase\ApiClientTestCase;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class VersionTest extends ApiClientTestCase
{
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
     * Test version
     *
     * @test
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function version(): void
    {
        /* Arrange */
        $endpoint = VersionController::API_ENDPOINT;
        $method = VersionController::API_ENDPOINT_METHOD;
        $versionFile = sprintf('%s/%s', self::$kernel->getProjectDir(), VersionController::PATH_VERSION);
        $version = file_get_contents($versionFile);

        /* Act */
        $response = $this->doRequest($endpoint, $method);
        $json = $response->getContent();
        $this->assertIsString($json);
        $object = json_decode($response->getContent());
        if ($version === false) {
            throw new Exception(sprintf('Unable to read file "%s" (%s:%d).', $versionFile, __FILE__, __LINE__));
        }

        /* Assert */
        $this->assertResponseIsSuccessful();
        $this->assertIsObject($object);
        if (is_object($object)) {
            $this->assertObjectHasAttribute('appVersion', $object);
            $this->assertObjectHasAttribute('phpVersion', $object);
            $this->assertObjectHasAttribute('symfonyVersion', $object);
            $this->assertFileExists($versionFile);
            if (property_exists($object, 'appVersion')) {
                $this->assertEquals(trim($version), $object->appVersion);
            }
        }
    }
}
