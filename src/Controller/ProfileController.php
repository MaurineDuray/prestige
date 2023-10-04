<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/member/{id}', name: 'member')]
    #[IsGranted("ROLE_USER")]
    public function index(User $id, UserRepository $repo, Request $request): Response
    {

        $member = $repo->findById($id);

        return $this->render('profile/index.html.twig', [
            'member' => $member,
        ]);
    }
}
