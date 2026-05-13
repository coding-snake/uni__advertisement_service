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
#[Route('/ad')]
class AdController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request        HTTP Request
     * @param AdRepository     $ad_repository Ad repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'ad_index',
        methods: ['GET']
    )]
    public function index(AdRepository $ad_repository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $count = $ad_repository->count([]);

dd($count);
        $pagination = $paginator->paginate(
            $ad_repository->queryAll(),
            $page,
            AdRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['ad.id', 'ad.created_at', 'ad.updated_at', 'ad.name'],
                'defaultSortFieldName' => 'ad.updated_at',
                'defaultSortDirection' => 'desc',
            ]
        );

        # if (count($pagination) === 0) {
        #     throw new \Exception('Pagination is empty');
        # }
    
        return $this->render('ad/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * View action.
     *
     * @param AdRepository $repository Ad repository
     * @param int              $id         Ad identifier
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'ad_view',
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
            'ad/view.html.twig',
            ['ad' => $ad]
        );
    }
}
