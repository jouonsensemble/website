<?php

namespace App\Controller;

use App\Service\MyLudo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(MyLudo $myLudo): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'latest_games' => $myLudo->getLatestArrivals(),
        ]);
    }
}
