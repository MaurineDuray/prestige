<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Events;
use App\Entity\Marques;
use App\Form\EventType;
use App\Form\MarqueType;
use App\Form\AccountType;
use App\Form\EventModifyType;
use App\Repository\UserRepository;
use App\Repository\EventsRepository;
use App\Repository\MarquesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/members', name: 'admin_membres')]
    #[IsGranted("ROLE_ADMIN")]
    public function members_admin(UserRepository $repo): Response
    {
        $membres = $repo->findAll();
        return $this->render('admin/members.html.twig', [
            'membres'=>$membres
        ]);
    }

    #[Route('/admin/marques', name: 'admin_marques')]
    #[IsGranted("ROLE_ADMIN")]
    public function members_marques(MarquesRepository $repo): Response
    {
        $marques = $repo->findAll();
        return $this->render('admin/marques.html.twig', [
            'marques'=>$marques
        ]);
    }

     /**
     * Permet d'afficher le formulaire de création de l'ajout d'une recette
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/marque/new', name: "add_marque")]
    #[IsGranted("ROLE_ADMIN")]
    public function createMarque(Request $request, EntityManagerInterface $manager): Response
    {
        $marque = new Marques();

        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**Gestion de l'image de couverture */
            $file = $form['logo']->getData();
            if (!empty($file)) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII;[^A-Za-z0-9_]remove;Lower()', $originalFilename);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $e->getMessage();
                }
                $marque->setLogo($newFilename);
            }

            $manager->persist($marque);
            $manager->flush();


            /**
             * Message flash pour alerter l'utilisateur de l'état de la tâche
             */
            $this->addFlash(
                'success',
                "La marque {$marque->getBrand()} a bien été enregistrée!"
            );

            return $this->redirectToRoute('admin_marques', [
                
            ]);
        }

        return $this->render("admin/addmarques.html.twig", [
            'myform' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une recette
     */
    #[Route('/marques/{id}/delete', name:"admin_marque_delete")]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteRecipe(Marques $marque, EntityManagerInterface $manager):Response
    {
        $this->addFlash(
            'success',
            "La recette {$marque->getBrand()} a été supprimée"
        );

      

        unlink($this->getParameter('uploads_directory').'/'.$marque->getLogo());
     
        
        $manager->remove($marque);
        $manager->flush();

        return $this->redirectToRoute('admin_marques');
    }

    #[Route('/admin/events', name: 'admin_events')]
    #[IsGranted("ROLE_ADMIN")]
    public function adminEvents(EventsRepository $repo): Response
    {
        $events = $repo->findAll();
        return $this->render('admin/events.html.twig', [
            'events'=>$events
        ]);
    }

     /**
     * Permet d'afficher le formulaire de création de l'ajout d'une recette
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/events/new', name: "add_events")]
    #[IsGranted("ROLE_ADMIN")]
    public function createEvent(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Events();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**Gestion de l'image de couverture */
            $file = $form['picture']->getData();
            if (!empty($file)) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII;[^A-Za-z0-9_]remove;Lower()', $originalFilename);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $e->getMessage();
                }
                $event->setPicture($newFilename);
            }

            $manager->persist($event);
            $manager->flush();


            /**
             * Message flash pour alerter l'utilisateur de l'état de la tâche
             */
            $this->addFlash(
                'success',
                "L'event {$event->getTitle()} a bien été enregistrée!"
            );

            return $this->redirectToRoute('admin_events', [
                
            ]);
        }

        return $this->render("admin/addevents.html.twig", [
            'myform' => $form->createView()
        ]);
    }

     /**
     * Permet de supprimer une recette
     */
    #[Route('/admin/events/{id}/delete', name:"admin_event_delete")]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteEvent(Events $event, EntityManagerInterface $manager):Response
    {
        $this->addFlash(
            'success',
            "L'event {$event->getTitle()} a été supprimée"
        );

       
        if ($event->getPicture()) {
            unlink($this->getParameter('uploads_directory').'/'.$event->getPicture());
        }
        
     
        
        $manager->remove($event);
        $manager->flush();

        return $this->redirectToRoute('admin_events');
    }

     /**
     * Permet d'éditer les informations d'un utilisateur connecté
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/edit/{id}", name:"admin_profile")]
    #[IsGranted("ROLE_ADMIN")]
    public function profile(Request $request, EntityManagerInterface $manager, User $user): Response
    {
       
        // pour la validation des images ou utiliser une validation Groups
        $fileName = $user->getAvatar();
        if(!empty($fileName))
        {
            $user->setAvatar(
                new File($this->getParameter('uploads_directory').'/'.$user->getAvatar())
            );
        } 

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            // gestion image 
            $user->setAvatar($fileName);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succès"
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render("admin/profileEdit.html.twig",[
            'myform' => $form->createView(),
            'user'=>$user
        ]);
    }

     /**
     * editer un évent
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/event/{id}", name:"admin_event")]
    #[IsGranted("ROLE_ADMIN")]
    public function eventModify(Request $request, EntityManagerInterface $manager, Events $event): Response
    {
        $form = $this->createForm(EventModifyType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
           
            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succès"
            );

            return $this->redirectToRoute('admin_events');
        }

        return $this->render("admin/editevents.html.twig",[
            'myform' => $form->createView(),
            'event'=>$event
        ]);
    }
}
