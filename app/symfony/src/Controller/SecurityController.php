<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\Security\LoginType;
use App\Form\Security\ResetType;
use App\Form\Security\RequestType;
use App\Manager\UserManager;
use App\Manager\TokenManager;
use App\Services\MessageService;
use App\Services\TokenService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/_easyadmin/login", name="admin_login", methods={"GET","POST"})
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function admin(AuthenticationUtils $authUtils): Response
    {
        $form = $this->createForm(LoginType::class, [
            '_username' => $authUtils->getLastUsername(),
        ]);

        return $this->render('admin/security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/login", name="user_login", methods={"GET","POST"})
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function user(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(LoginType::class, [
            '_username' => $authUtils->getLastUsername(),
        ]);

        return $this->render('middle/security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/password/request", name="user_request", methods={"GET","POST"})
     *
     * @param AuthenticationUtils $authUtils
     * @param Request             $request
     * @param TokenManager        $tokenManager
     * @param MessageService      $messageService
     * @param UserManager         $userManager
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function request(
        AuthenticationUtils $authUtils,
        Request $request,
        TokenManager $tokenManager,
        MessageService $messageService,
        UserManager $userManager
    ): Response {
        $form = $this->createForm(RequestType::class, [
            '_username' => $authUtils->getLastUsername(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userManager->loadByUsername($form->getData()['_username']);

            if ($user instanceof User) {
                $tokenManager->create($user);
                $messageService->addSuccess('message.flash.resetpasswordsave');
            } else {
                $messageService->addError('message.flash.resetpasswordnosave');
            }
        }

        return $this->render('middle/security/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/password/reset/{token}", name="user_reset", methods={"GET","POST"})
     * @ParamConverter("token", options={"mapping"={"token"="value"}})
     *
     * @param Request        $request
     * @param MessageService $messageService
     * @param TokenService   $tokenService
     * @param UserManager    $userManager
     * @param Token          $token
     *
     * @return Response
     */
    public function reset(
        Request $request,
        MessageService $messageService,
        TokenService $tokenService,
        UserManager $userManager,
        Token $token
    ): Response {
        if (!$tokenService->isValid($token)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ResetType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $userManager->setPassword($token->getUser(), $data['password']);

            $messageService->addSuccess('message.flash.resetpassword');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('middle/security/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/_easyadmin/logout", name="admin_logout", methods={"GET"})
     * @Route("/cssr/logout", name="middle_logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
