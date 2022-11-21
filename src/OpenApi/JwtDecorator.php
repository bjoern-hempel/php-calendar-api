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

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

/**
 * Class JwtDecorator
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-12)
 * @since 0.1.1 (2022-11-12) Upgrade to symfony 6.1
 * @since 0.1.0 First version.
 */
final class JwtDecorator implements OpenApiFactoryInterface
{
    public const API_ENDPOINT = '/api/v1/token/get';

    public const API_ENDPOINT_METHOD = 'POST';

    /**
     * JwtDecorator constructor.
     *
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * Invoke magic call.
     *
     * @param string[] $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'user1@domain.tld',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password1',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath(self::API_ENDPOINT, $pathItem);

        return $openApi;
    }
}
