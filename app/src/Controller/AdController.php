<?php
/**
 * Ad controller.
 */

namespace App\Controller;

use App\Entity\Ad;
use App\Service\AdService;
use App\Service\AdServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class AdController.
 */
#[Route('/ads')]
class AdController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly AdServiceInterface $adService)
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
    public function read(Ad $ad): Response
    {
        return $this->render(
            'ads/read.html.twig',
            ['ad' => $ad]
        );
    }
}
