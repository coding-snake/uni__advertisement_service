<?php
/**
 * Ad controller.
 */

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\Type\AdType;
use App\Security\Voter\AdVoter;
use App\Service\AdServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdController.
 * 
 * @param AdServiceInterface $adService Ad service
 * @param TranslatorInterface      $translator
 */
#[Route('/ads')]
class AdController extends AbstractController
{
    /**
     * Constructor.
     * 
     * @param AdServiceInterface $adService Ad service
     */
    public function __construct(private readonly AdServiceInterface $adService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'ad_index',
        methods: ['GET']
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->adService->getPaginatedList($page);

        return $this->render('ads/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * View action.
     *
     * @param Ad $ad Ad entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'ad_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    #[IsGranted(AdVoter::VIEW, subject: 'ad')]
    public function read(Ad $ad): Response
    {
        return $this->render(
            'ads/read.html.twig',
            ['ad' => $ad]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'ad_create',
        methods: ['GET', 'POST']
    )]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $ad = new Ad();
        $ad->setAuthor($user);
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adService->save($ad);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('ad_index');
        }

        return $this->render(
            'ads/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Ad $ad Ad entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'ad_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(AdVoter::EDIT, subject: 'ad')]
    public function edit(Request $request, Ad $ad): Response
    {
        $form = $this->createForm(
            AdType::class,
            $ad,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('ad_edit', ['id' => $ad->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adService->save($ad);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('ad_index');
        }

        return $this->render(
            'ads/edit.html.twig',
            [ 'form' => $form->createView(),
                'ad' => $ad,
            ]
        );
    }
    
    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Ad $ad Ad entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'ad_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(AdVoter::DELETE, subject: 'ad')]
    public function delete(Request $request, Ad $ad): Response
    {
        $form = $this->createForm(FormType::class, $ad, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('ad_delete', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adService->delete($ad);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('ad_index');
        }

        return $this->render(
            'ads/delete.html.twig',
            [
                'form' => $form->createView(),
                'ad' => $ad,
            ]
        );
    }
}
