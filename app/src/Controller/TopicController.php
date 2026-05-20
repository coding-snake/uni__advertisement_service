<?php
/**
 * Topic controller.
 */

namespace App\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Service\TopicService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class Controller.
 */
#[Route('/topics')]
class TopicController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly TopicServiceInterface $topicService)
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
}
