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

namespace App\Controller;

use App\Service\ConfigService;
use App\Service\VersionService;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SecurityController
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-21)
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * SecurityController constructor.
     *
     * @param TranslatorInterface $translator
     * @param ConfigService $configService
     * @param VersionService $versionService
     */
    public function __construct(protected TranslatorInterface $translator, protected ConfigService $configService, protected VersionService $versionService)
    {
    }

    /**
     * Login method.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/admin/login', name: 'app_admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last email entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/page/login.html.twig', [

            /* title and other variables */
            'page_title' => $this->configService->getConfig(ConfigService::PARAMETER_NAME_BACKEND_TITLE_LOGIN),
            'version' => $this->versionService->getVersion(),

            /* labels */
            'username_label' => $this->translator->trans('admin.login.labelEmail'),
            'password_label' => $this->translator->trans('admin.login.labelPassword'),
            'sign_in_label' => $this->translator->trans('admin.login.labelLogin'),

            /* parameter */
            'username_parameter' => 'email',
            'password_parameter' => 'password',

            /* prefilled fields */
            'last_username' => $lastUsername,

            /* other configs */
            'error' => $error,
            'csrf_token_intention' => 'login',
        ]);
    }

    #[Route(path: '/admin/logout', name: 'app_admin_logout')]
    public function logout(): never
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
