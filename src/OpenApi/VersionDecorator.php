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

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

/**
 * Class VersionDecorator
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-26)
 * @package App\Controller
 */
final class VersionDecorator implements OpenApiFactoryInterface
{
    public const API_ENDPOINT = '/api/v1/version';

    /**
     * VersionDecorator constructor.
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

        $schemas['Version'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'version' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Version',
            get: new Model\Operation(
                operationId: 'versionItem',
                tags: ['Version'],
                responses: [
                    '200' => [
                        'description' => 'Current version',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Version',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Gets the current version of this API.'
            ),
        );

        $openApi->getPaths()->addPath(self::API_ENDPOINT, $pathItem);

        return $openApi;
    }
}
