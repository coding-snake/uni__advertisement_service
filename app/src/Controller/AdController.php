<?php
/**
 * Ad controller.
 */

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AdController.
 */
#[Route('/ads')]
class AdController extends AbstractController
{

    #[Route(
        name: 'ad_index',
        methods: ['GET']
    )]
    public function index(Request $request, AdRepository $adRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $adRepository->queryAll(),
            $request->query->getInt('page', 1),
            AdRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['ad.id', 'ad.createdAt', 'ad.updatedAt', 'ad.name', 'topic.name'],
                'defaultSortFieldName' => 'ad.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        return $this->render('ads/index.html.twig', ['pagination' => $pagination]);
    }


    #[Route(
        '/{id}',
        name: 'ad_read',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(AdRepository $repository, int $id): Response
    {
        $ad = $repository->findOneById($id);

        if (null === $ad) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'ads/read.html.twig',
            ['ad' => $ad]
        );
    }
}
