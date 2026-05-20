<?php
/**
 * Tag controller.
 */

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class Controller.
 */
#[Route('/tags')]
class TagController extends AbstractController
{

    #[Route(
        name: 'tag_index',
        methods: ['GET']
    )]
    public function index(Request $request, TagRepository $tagRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $tagRepository->queryAll(),
            $request->query->getInt('page', 1),
            TagRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['tag.id', 'tag.createdAt', 'tag.updatedAt', 'topic.name'],
                'defaultSortFieldName' => 'tag.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        return $this->render('tags/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route(
        '/{id}',
        name: 'tag_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(TagRepository $repository, int $id): Response
    {
        $tag = $repository->findOneById($id);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'tags/read.html.twig',
            ['tag' => $tag]
        );
    }
}
