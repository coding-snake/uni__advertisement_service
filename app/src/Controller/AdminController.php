<?php
/**
 * Admin Controller
 */
namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UsernameType;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController.
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route('/', name: 'admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * User index action (list of users).
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        '/users',
        name: 'admin_user_index',
        methods: ['GET']
    )]
    public function userIndex(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('admin/user.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Edit user's username action.
     *
     * @param Request                $request       HTTP request
     * @param User                   $user          User entity
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/users/{id}/edit-username', name: 'admin_user_edit_username', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function editUsername(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsernameType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.username_changed_successfully'));

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/edit_username.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Edit user's password action.
     *
     * @param Request                     $request        HTTP request
     * @param User                        $user           User entity
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param EntityManagerInterface      $entityManager  Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/users/{id}/edit-password', name: 'admin_user_edit_password', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

            $user->setPassword($hashedPassword);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.password_changed_successfully'));

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/edit_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
