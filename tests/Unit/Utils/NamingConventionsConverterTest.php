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

namespace App\Tests\Unit\Utils;

use App\Utils\NamingConventionsConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class NamingConventionsConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-31)
 * @package App\Tests\Unit\Utils
 */
final class NamingConventionsConverterTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test NamingConventionsConverter: $method
     * @param int $number
     * @param string $method
     * @param string|array<int, string> $given
     * @param string|array<int, string> $expected
     */
    public function wrapper(int $number, string $method, string|array $given, string|array $expected): void
    {
        /* Arrange */

        /* Act */
        $namingConventionsConverter = new NamingConventionsConverter($given);
        $callback = [$namingConventionsConverter, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods($namingConventionsConverter));
        $this->assertIsCallable($callback);
        $this->assertSame($expected, call_user_func($callback));
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, array<int, string>|string|int>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Basic word
             */
            [++$number, 'getRaw', 'group', 'group', ],
            [++$number, 'getWords', 'group', ['group', ], ],
            [++$number, 'getTitle', 'group', 'Group', ],
            [++$number, 'getPascalCase', 'group', 'Group', ],
            [++$number, 'getCamelCase', 'group', 'group', ],
            [++$number, 'getUnderscored', 'group', 'group', ],
            [++$number, 'getConstant', 'group', 'GROUP', ],
            [++$number, 'getConfig', 'group', 'group', ],
            [++$number, 'getSeparated', 'group', 'group', ],

            /**
             * Words
             */
            [++$number, 'getRaw', ['group', 'private', ], ['group', 'private', ], ],
            [++$number, 'getWords', ['group', 'private', ], ['group', 'private', ], ],
            [++$number, 'getTitle', ['group', 'private', ], 'Group Private', ],
            [++$number, 'getPascalCase', ['group', 'private', ], 'GroupPrivate', ],
            [++$number, 'getCamelCase', ['group', 'private', ], 'groupPrivate', ],
            [++$number, 'getUnderscored', ['group', 'private', ], 'group_private', ],
            [++$number, 'getConstant', ['group', 'private', ], 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', ['group', 'private', ], 'group.private', ],
            [++$number, 'getSeparated', ['group', 'private', ], 'group-private', ],

            /**
             * Title
             */
            [++$number, 'getRaw', 'Group Private', 'Group Private', ],
            [++$number, 'getWords', 'Group Private', ['group', 'private', ], ],
            [++$number, 'getTitle', 'Group Private', 'Group Private', ],
            [++$number, 'getPascalCase', 'Group Private', 'GroupPrivate', ],
            [++$number, 'getCamelCase', 'Group Private', 'groupPrivate', ],
            [++$number, 'getUnderscored', 'Group Private', 'group_private', ],
            [++$number, 'getConstant', 'Group Private', 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', 'Group Private', 'group.private', ],
            [++$number, 'getSeparated', 'Group Private', 'group-private', ],

            /**
             * PascalCase
             */
            [++$number, 'getRaw', 'GroupPrivate', 'GroupPrivate', ],
            [++$number, 'getWords', 'GroupPrivate', ['group', 'private', ], ],
            [++$number, 'getTitle', 'GroupPrivate', 'Group Private', ],
            [++$number, 'getPascalCase', 'GroupPrivate', 'GroupPrivate', ],
            [++$number, 'getCamelCase', 'GroupPrivate', 'groupPrivate', ],
            [++$number, 'getUnderscored', 'GroupPrivate', 'group_private', ],
            [++$number, 'getConstant', 'GroupPrivate', 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', 'GroupPrivate', 'group.private', ],
            [++$number, 'getSeparated', 'GroupPrivate', 'group-private', ],

            /**
             * camelCase
             */
            [++$number, 'getRaw', 'groupPrivate', 'groupPrivate', ],
            [++$number, 'getWords', 'groupPrivate', ['group', 'private', ], ],
            [++$number, 'getTitle', 'groupPrivate', 'Group Private', ],
            [++$number, 'getPascalCase', 'groupPrivate', 'GroupPrivate', ],
            [++$number, 'getCamelCase', 'groupPrivate', 'groupPrivate', ],
            [++$number, 'getUnderscored', 'groupPrivate', 'group_private', ],
            [++$number, 'getConstant', 'groupPrivate', 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', 'groupPrivate', 'group.private', ],
            [++$number, 'getSeparated', 'groupPrivate', 'group-private', ],

            /**
             * Underscored
             */
            [++$number, 'getRaw', 'group_private', 'group_private', ],
            [++$number, 'getWords', 'group_private', ['group', 'private', ], ],
            [++$number, 'getTitle', 'group_private', 'Group Private', ],
            [++$number, 'getPascalCase', 'group_private', 'GroupPrivate', ],
            [++$number, 'getCamelCase', 'group_private', 'groupPrivate', ],
            [++$number, 'getUnderscored', 'group_private', 'group_private', ],
            [++$number, 'getConstant', 'group_private', 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', 'group_private', 'group.private', ],
            [++$number, 'getSeparated', 'group_private', 'group-private', ],

            /**
             * Constant
             */
            [++$number, 'getRaw', 'GROUP_PRIVATE', 'GROUP_PRIVATE', ],
            [++$number, 'getWords', 'GROUP_PRIVATE', ['group', 'private', ], ],
            [++$number, 'getTitle', 'GROUP_PRIVATE', 'Group Private', ],
            [++$number, 'getPascalCase', 'GROUP_PRIVATE', 'GroupPrivate', ],
            [++$number, 'getCamelCase', 'GROUP_PRIVATE', 'groupPrivate', ],
            [++$number, 'getUnderscored', 'GROUP_PRIVATE', 'group_private', ],
            [++$number, 'getConstant', 'GROUP_PRIVATE', 'GROUP_PRIVATE', ],
            [++$number, 'getConfig', 'GROUP_PRIVATE', 'group.private', ],
            [++$number, 'getSeparated', 'GROUP_PRIVATE', 'group-private', ],

            /**
             * Words (Multiple)
             */
            [++$number, 'getRaw', ['group', 'private', 'as', 'multiple', ], ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getWords', ['group', 'private', 'as', 'multiple', ], ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', ['group', 'private', 'as', 'multiple', ], 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', ['group', 'private', 'as', 'multiple', ], 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', ['group', 'private', 'as', 'multiple', ], 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', ['group', 'private', 'as', 'multiple', ], 'group_private_as_multiple', ],
            [++$number, 'getConstant', ['group', 'private', 'as', 'multiple', ], 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', ['group', 'private', 'as', 'multiple', ], 'group.private.as.multiple', ],
            [++$number, 'getSeparated', ['group', 'private', 'as', 'multiple', ], 'group-private-as-multiple', ],

            /**
             * Title (Multiple)
             */
            [++$number, 'getRaw', 'Group Private As Multiple', 'Group Private As Multiple', ],
            [++$number, 'getWords', 'Group Private As Multiple', ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', 'Group Private As Multiple', 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', 'Group Private As Multiple', 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', 'Group Private As Multiple', 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', 'Group Private As Multiple', 'group_private_as_multiple', ],
            [++$number, 'getConstant', 'Group Private As Multiple', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', 'Group Private As Multiple', 'group.private.as.multiple', ],
            [++$number, 'getSeparated', 'Group Private As Multiple', 'group-private-as-multiple', ],

            /**
             * PascalCase (Multiple)
             */
            [++$number, 'getRaw', 'GroupPrivateAsMultiple', 'GroupPrivateAsMultiple', ],
            [++$number, 'getWords', 'GroupPrivateAsMultiple', ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', 'GroupPrivateAsMultiple', 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', 'GroupPrivateAsMultiple', 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', 'GroupPrivateAsMultiple', 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', 'GroupPrivateAsMultiple', 'group_private_as_multiple', ],
            [++$number, 'getConstant', 'GroupPrivateAsMultiple', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', 'GroupPrivateAsMultiple', 'group.private.as.multiple', ],
            [++$number, 'getSeparated', 'GroupPrivateAsMultiple', 'group-private-as-multiple', ],

            /**
             * camelCase (Multiple)
             */
            [++$number, 'getRaw', 'groupPrivateAsMultiple', 'groupPrivateAsMultiple', ],
            [++$number, 'getWords', 'groupPrivateAsMultiple', ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', 'groupPrivateAsMultiple', 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', 'groupPrivateAsMultiple', 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', 'groupPrivateAsMultiple', 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', 'groupPrivateAsMultiple', 'group_private_as_multiple', ],
            [++$number, 'getConstant', 'groupPrivateAsMultiple', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', 'groupPrivateAsMultiple', 'group.private.as.multiple', ],
            [++$number, 'getSeparated', 'groupPrivateAsMultiple', 'group-private-as-multiple', ],

            /**
             * Underscored (Multiple)
             */
            [++$number, 'getRaw', 'group_private_as_multiple', 'group_private_as_multiple', ],
            [++$number, 'getWords', 'group_private_as_multiple', ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', 'group_private_as_multiple', 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', 'group_private_as_multiple', 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', 'group_private_as_multiple', 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', 'group_private_as_multiple', 'group_private_as_multiple', ],
            [++$number, 'getConstant', 'group_private_as_multiple', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', 'group_private_as_multiple', 'group.private.as.multiple', ],
            [++$number, 'getSeparated', 'group_private_as_multiple', 'group-private-as-multiple', ],

            /**
             * Constant (Multiple)
             */
            [++$number, 'getRaw', 'GROUP_PRIVATE_AS_MULTIPLE', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getWords', 'GROUP_PRIVATE_AS_MULTIPLE', ['group', 'private', 'as', 'multiple', ], ],
            [++$number, 'getTitle', 'GROUP_PRIVATE_AS_MULTIPLE', 'Group Private As Multiple', ],
            [++$number, 'getPascalCase', 'GROUP_PRIVATE_AS_MULTIPLE', 'GroupPrivateAsMultiple', ],
            [++$number, 'getCamelCase', 'GROUP_PRIVATE_AS_MULTIPLE', 'groupPrivateAsMultiple', ],
            [++$number, 'getUnderscored', 'GROUP_PRIVATE_AS_MULTIPLE', 'group_private_as_multiple', ],
            [++$number, 'getConstant', 'GROUP_PRIVATE_AS_MULTIPLE', 'GROUP_PRIVATE_AS_MULTIPLE', ],
            [++$number, 'getConfig', 'GROUP_PRIVATE_AS_MULTIPLE', 'group.private.as.multiple', ],
            [++$number, 'getSeparated', 'GROUP_PRIVATE_AS_MULTIPLE', 'group-private-as-multiple', ],

            /**
             * Specials
             */
            [++$number, 'getConfig', '_BLANK', 'blank', ],
            [++$number, 'getConfig', '_BLANK_VALUE', 'blank.value', ],

        ];
    }
}
