<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        // AuthenticationUtils offre des utilitaires qui concerne l'authentification
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        // dump($error);
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout(){
        // symfony gère le logout
        // il faut ajouter des trucs dans le security.yaml
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte à bien été créé ! Vous pouvez maintenant vous connecter !'
            );

            return $this->redirectToRoute('account_login');
        }



        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Reponse
     */
    public function profile(Request $request, EntityManagerInterface $manager){
        // $this->getUser() donne l'utilisateur connecté
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user); // pas necessaire de persister une entité qui existe déjà
            $manager->flush();

            $this->addFlash(
                'success',
                'Les données du profil on été enregistrées avec succès'
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){
        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                // Gérér l'erreur
                $form->get('oldPassword')->addError(new FormError('Votre ancien mot de passe n\'est pas correct')); 
                // récupère le champ OldPassword et lui ajoute une erreur
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe à bien été modifié !'
                );

                return $this->redirectToRoute('homepage');
            }
            
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }


    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     *
     * @Route("/account/bookings", name="account_bookings")
     * 
     * @return Response
     */
    public function bookings() {
        return $this->render('account/bookings.html.twig');
    }
}
