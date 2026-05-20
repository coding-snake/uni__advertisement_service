<?php
/**
 * Tag controller.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class Controller.
 */
#[Route('/tags')]
class TagController extends AbstractController
{
    /**
     * Constructor
     */
    public function __construct(private readonly TagServiceInterface $tagService)
    {
    }

    /**
     * Index action
     *
     * @param int $page Page number
     * @return Response HTTP response
     */
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->tagService->getPaginatedList($page);

        return $this->render('tags/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Read action.
     *
     * @param Tag $tag Tag entity
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'tag_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function read(Tag $tag): Response
    {
        return $this->render(
            'tags/read.html.twig',
            ['tag' => $tag]
        );
    }
}
