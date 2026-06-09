<?php
/**
 * Home Controller
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        '/',
        name: 'home',
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
