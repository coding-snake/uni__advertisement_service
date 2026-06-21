<?php

/**
 * Ad Controller.
 */

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Form\Type\AdType;
use App\Repository\TopicRepository;
use App\Security\Voter\AdVoter;
use App\Service\AdServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
 */
#[Route('/ads')]
class AdController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param AdServiceInterface  $adService  Ad service
     * @param TranslatorInterface $translator Translator
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
     * Ads per topic action.
     *
     * @param Topic $topic Topic entity
     * @param int   $page  Page number
     *
     * @return Response HTTP response
     */
    #[Route('/topics/{id}/ads', name: 'ads_per_topic', methods: ['GET'])]
    public function adsPerTopic(Topic $topic, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->adService->getPaginatedListByTopic($topic, $page);

        return $this->render('ads/topic.html.twig', [
            'pagination' => $pagination,
            'topic' => $topic,
        ]);
    }

    /**
     * Ads per tag action.
     *
     * @param Tag $tag  Tag entity
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/tags/{id}/ads', name: 'ads_per_tag', methods: ['GET'])]
    public function adsPerTag(Tag $tag, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->adService->getPaginatedListByTag($tag, $page);

        return $this->render('ads/tag.html.twig', [
            'pagination' => $pagination,
            'tag' => $tag,
        ]);
    }

    /**
     * Read action.
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
     * Toggle verification action.
     *
     * @param Ad                     $ad            Ad entity
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/toggle-verification', name: 'ad_toggle_verification', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleVerification(Ad $ad, EntityManagerInterface $entityManager): Response
    {
        $ad->setVerified(!$ad->getVerified());

        $entityManager->flush();

        return $this->redirectToRoute('ad_read', [
            'id' => $ad->getId(),
        ]);
    }

    /**
     * Create action.
     *
     * @param Request         $request         HTTP request
     * @param TopicRepository $topicRepository Topic repository
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'ad_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted(AdVoter::CREATE)]
    public function create(Request $request, TopicRepository $topicRepository): Response
    {
        $topicCount = $topicRepository->count([]);
        if (0 === $topicCount) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.topic_must_exist_first')
            );

            return $this->redirectToRoute('topic_create');
        }

        $ad = new Ad();

        if ($this->getUser()) {
            $ad->setAuthor($this->getUser());
            $ad->setVerified(true);
        } else {
            $ad->setVerified(false);
        }

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
     * @param Request $request HTTP request
     * @param Ad      $ad      Ad entity
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
            $ad->setVerified(!$ad->getVerified());
            $this->adService->save($ad);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('ad_index');
        }

        return $this->render(
            'ads/edit.html.twig',
            ['form' => $form->createView(),
                'ad' => $ad,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Ad      $ad      Ad entity
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
