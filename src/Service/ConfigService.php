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

namespace App\Service;

use App\Exception\ConfigurationNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ConfigService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-22)
 * @package App\Command
 */
class ConfigService
{
    public const PARAMETER_NAME_BACKEND_TITLE_MAIN = 'backend.title.main';

    public const PARAMETER_NAME_BACKEND_TITLE_LOGIN = 'backend.title.login';

    protected ParameterBagInterface $parameterBag;

    /**
     * ConfigService constructor
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Get config from parameter bag.
     *
     * @param string $name
     * @return string
     */
    public function getConfig(string $name): string
    {
        if (!$this->parameterBag->has($name)) {
            throw new ConfigurationNotFoundException($name);
        }

        return strval($this->parameterBag->get($name));
    }
}
