<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AccountType;
use App\Entity\LogoModify;
use App\Form\ImgModifyType;
use App\Form\LogoModifyType;
use App\Entity\UserImgModify;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Permet à l'utilisateur de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        return $this->render('account/index.html.twig', [
            'hasError' => $error !==null,
            'username' => $username
        ]);
    }

    /**
     * Permet à l'utisilateur de se déconnecter
     *
     * @return void
     */
    #[Route("/logout", name:"account_logout")]
    public function logout():void
    {
        //
    }

    /**
     * Permet d'affiche le profil de l'utilisateur connecté
     *
     * @return Response
     */
    #[Route("/profile", name:"profile")]
    #[IsGranted("ROLE_USER")]
    public function myAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

     /**
     * Permet d'afficher le formulaire de création de l'ajout d'une recette
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/member/new', name: "add_member")]
    #[IsGranted("ROLE_ADMIN")]
    public function createEvent(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher, MailerInterface $mailer,): Response
    {
        $member = new User();

        $form = $this->createForm(UserType::class, $member);
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
                $member->setLogo($newFilename);
            }

            $file = $form['avatar']->getData();
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
                $member->setAvatar($newFilename);
            }

            $hash = $hasher->hashPassword($member, $member->getPassword());
            $member->setPassword($hash);

            $password= "User";

            // mail send
            // $email = (new TemplatedEmail())
            // ->from('site@prestigebusinessclub.be')
            // ->to($member->getEmail())
            // ->subject('Inscription Prestige Business Club')
            // ->htmlTemplate('mails/registration.html.twig')
            // ->context([
            //     'user'=>$member,
            //     'password'=>$password
            // ]);

            // $mailer->send($email);

            $manager->persist($member);
            $manager->flush();


            /**
             * Message flash pour alerter l'utilisateur de l'état de la tâche
             */
            $this->addFlash(
                'success',
                "Le membre {$member->getEmail()} a bien été enregistrée!"
            );

            return $this->redirectToRoute('admin_membres');
        }

        return $this->render("admin/register.html.twig", [
            'myform' => $form->createView()
        ]);
    }

     /**
     * Permet de supprimer une recette
     */
    #[Route('/admin/marques/{id}/delete', name:"admin_user_delete")]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteRecipe(User $user, EntityManagerInterface $manager):Response
    {
        $this->addFlash(
            'success',
            "Le membre {$user->getLastname()} - {$user->getFirstname()} a été supprimée"
        );

        // suppression des images de la galerie

        if ($user->getLogo()) {
            unlink($this->getParameter('uploads_directory').'/'.$user->getLogo());
        }
        if ($user->getAvatar()) {
            unlink($this->getParameter('uploads_directory').'/'.$user->getAvatar());
        }
        
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_membres');
    }

    /**
     * Permet de changer de mot de passe 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/account/password-update", name:'account_password')]
    #[IsGranted("ROLE_USER")]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérifier que le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );

                return $this->redirectToRoute('profile');
            }
        }


        return $this->render("user/password.html.twig",[
            'myform' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer les informations d'un utilisateur connecté
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/edit", name:"account_profile")]
    #[IsGranted("ROLE_USER")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // récup l'utilisateur connecté

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

        return $this->render("profile/profileEdit.html.twig",[
            'myform' => $form->createView(),
        ]);
    }

     /**
     * Permet de modifier l'image de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/imgmodify", name:"account_modifimg")]
    #[IsGranted("ROLE_USER")]
    public function imgModify(Request $request, EntityManagerInterface $manager): Response
    {
        $imgModify = new UserImgModify();
        $user = $this->getUser(); 
        $form = $this->createForm(ImgModifyType::class, $imgModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // supprimer l'image dans le dossier
            if(!empty($user->getAvatar()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$user->getAvatar());
            }

            $file = $form['newAvatar']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().".".$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setAvatar($newFilename);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre avatar a bien été modifié'
            );

            return $this->redirectToRoute('profile');

        }

        return $this->render("profile/avatarEdit.html.twig",[
            'myform' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier l'image de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/logomodify", name:"account_logomodify")]
    #[IsGranted("ROLE_USER")]
    public function logoModify(Request $request, EntityManagerInterface $manager): Response
    {
        $logoModify = new LogoModify();
        $user = $this->getUser(); 
        $form = $this->createForm(LogoModifyType::class, $logoModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // supprimer l'image dans le dossier
            if(!empty($user->getLogo()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$user->getLogo());
            }

            $file = $form['newLogo']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().".".$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setLogo($newFilename);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre Logo a bien été modifié'
            );

            return $this->redirectToRoute('profile');

        }

        return $this->render("profile/logoModify.html.twig",[
            'myform' => $form->createView()
        ]);
    }
}
