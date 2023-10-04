<?php

namespace App\Controller;

use App\Repository\MarquesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(MarquesRepository $repo): Response
    {
        $marques = $repo->findAll();
        return $this->render('home.html.twig', [
           'marques'=>$marques
        ]);
    }
}
