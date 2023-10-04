<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MembersController extends AbstractController
{
    #[Route('/members', name: 'members')]
    #[IsGranted("ROLE_USER")]
    public function index(UserRepository $repo): Response
    {
        $members = $repo->findAll();
        return $this->render('members/index.html.twig', [
            'members'=>$members
        ]);
    }
}
