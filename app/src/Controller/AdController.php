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
     * @param AdRepository     $adRepository ad repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     */
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
                'sortFieldAllowList' => ['ad.id', 'ad.createdAt', 'ad.updatedAt', 'ad.name'],
                'defaultSortFieldName' => 'ad.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );

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
