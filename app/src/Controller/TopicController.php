<?php
/**
 * Topic controller.
 */

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class Controller.
 */
#[Route('/topics')]
class TopicController extends AbstractController
{

    #[Route(
        name: 'topic_index',
        methods: ['GET']
    )]
    public function index(Request $request, TopicRepository $topicRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $topicRepository->queryAll(),
            $request->query->getInt('page', 1),
            TopicRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['topic.id', 'topic.createdAt', 'topic.updatedAt', 'topic.name'],
                'defaultSortFieldName' => 'topic.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        return $this->render('topics/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route(
        '/{id}',
        name: 'topic_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(TopicRepository $repository, int $id): Response
    {
        $topic = $repository->findOneById($id);

        if (null === $topic) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'topics/read.html.twig',
            ['topic' => $topic]
        );
    }
}
