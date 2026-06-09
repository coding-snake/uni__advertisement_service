<?php
/**
 * Topic Controller
 */
namespace App\Controller;

use App\Entity\Topic;
use App\Form\Type\TopicType;
use App\Security\Voter\TopicVoter;
use App\Service\TopicServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TopicController.
 */
#[Route('/topics')]
class TopicController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TopicServiceInterface $topicService Topic service
     * @param TranslatorInterface   $translator   Translator
     */
    public function __construct(private readonly TopicServiceInterface $topicService, private readonly TranslatorInterface $translator)
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
        name: 'topic_index',
        methods: ['GET']
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->topicService->getPaginatedList($page);

        return $this->render('topics/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Read action.
     *
     * @param Topic $topic Topic entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'topic_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function read(Topic $topic): Response
    {
        return $this->render(
            'topics/read.html.twig',
            ['topic' => $topic]
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
        name: 'topic_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted(TopicVoter::CREATE)]
    public function create(Request $request): Response
    {
        $topic = new Topic();

        if ($this->getUser()) {
            $topic->setAuthor($this->getUser());
        }

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->topicService->save($topic);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('topic_index');
        }

        return $this->render(
            'topics/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Topic   $topic   Topic entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'topic_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(TopicVoter::EDIT, subject: 'topic')]
    public function edit(Request $request, Topic $topic): Response
    {
        $form = $this->createForm(
            TopicType::class,
            $topic,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('topic_edit', ['id' => $topic->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->topicService->save($topic);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('topic_index');
        }

        return $this->render(
            'topics/edit.html.twig',
            [
                'form' => $form->createView(),
                'topic' => $topic,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Topic   $topic   Topic entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'topic_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(TopicVoter::DELETE, subject: 'topic')]
    public function delete(Request $request, Topic $topic): Response
    {
        if (!$this->topicService->canBeDeleted($topic)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.topic_contains_ads')
            );

            return $this->redirectToRoute('topic_index');
        }

        $form = $this->createForm(FormType::class, $topic, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('topic_delete', ['id' => $topic->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->topicService->delete($topic);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('topic_index');
        }

        return $this->render(
            'topics/delete.html.twig',
            [
                'form' => $form->createView(),
                'topic' => $topic,
            ]
        );
    }
}
