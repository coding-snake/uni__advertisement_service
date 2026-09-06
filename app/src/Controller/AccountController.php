<?php

/**
 * Account Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UsernameType;
use App\Form\Type\ChangePasswordType;
use App\Service\AccountServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AccountController.
 */
#[Route('/account')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route('/', name: 'account_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * Change username action.
     *
     * @param Request                 $request        HTTP request
     * @param AccountServiceInterface $accountService Account service
     *
     * @return Response HTTP response
     */
    #[Route('/change_username', name: 'change_username', methods: ['GET', 'POST'])]
    public function changeUsername(Request $request, AccountServiceInterface $accountService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UsernameType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountService->save($user);

            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/change_username.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Change password action.
     *
     * @param Request                 $request        HTTP request
     * @param AccountServiceInterface $accountService Account service
     *
     * @return Response HTTP response
     */
    #[Route('/change_password', name: 'change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, AccountServiceInterface $accountService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $newPassword */
            $newPassword = $form->get('plainPassword')->getData();

            $accountService->changePassword($user, $newPassword);

            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
