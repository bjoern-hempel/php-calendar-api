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

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\Event;
use App\Entity\Image;
use App\Entity\User;
use App\Service\SecurityService;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Class CurrentUserExtension (used by JWT/API platform)
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.2 (2022-11-19)
 * @since 0.1.2 (2022-11-19) Update ApiPlatform.
 * @since 0.1.1 (2022-01-29) Possibility to disable the JWT locally for debugging processes (#45)
 * @since 0.1.0 First version.
 * @package App\Doctrine
 */
final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private SecurityService $securityService;

    private ParameterBagInterface $parameterBag;

    public const PARAMETER_NAME_JWT_ROLE = 'jwt.role';

    /**
     * CurrentUserExtension constructor.
     *
     * @param SecurityService $securityService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(SecurityService $securityService, ParameterBagInterface $parameterBag)
    {
        $this->securityService = $securityService;

        $this->parameterBag = $parameterBag;
    }

    /**
     * Applies addWhere method to collection operations.
     *
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array<int, mixed> $context
     * @throws Exception
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * Applies addWhere method to item operations.
     *
     * Be aware to add a security part to itemOperations of your entity class!
     *
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array<int|string, mixed> $identifiers
     * @param Operation|null $operation
     * @param string[] $context
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
    }

    /**
     * Adds current user to query builder.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     * @throws Exception
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        /* Only valid for User entity. */
        if (!in_array($resourceClass, [Calendar::class, CalendarImage::class, Event::class, Image::class, User::class])) {
            return;
        }

        /* Admin role can do this. */
        if ($this->securityService->isGrantedByAnAdmin()) {
            return;
        }

        /* Get current user. */
        $user = $this->securityService->getUser();

        /* JWT is disabled */
        if ($this->parameterBag->get(self::PARAMETER_NAME_JWT_ROLE) === AuthenticatedVoter::PUBLIC_ACCESS) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf(User::class === $resourceClass ? '%s.id = :current_user' : '%s.user = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user->getId());
    }
}
