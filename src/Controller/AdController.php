<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        // $repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll(); // rassemble toutes les annonces trouvée dans le repo Ad

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


    /**
     * Permet de créer une annonce
     *
     * @Route("/ads/new", name="ads_create")
     * @return Response
     */
    public function create(Request $request){
        $ad = new Ad();

        // en paramètre, la classe du formulaire ( AdType ) et l'annonce ($ad)
        $form = $this->createForm(AdType::class, $ad);

        // handleRequest -> gère la requête
        $form->handleRequest($request);



        // TEST
        // -----------------------
        // $this->addFlash(
        //     'success',
        //     "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
        // );        
        // $this->addFlash(
        //     'success',
        //     "Deuxième succès"
        // );        
        // $this->addFlash(
        //     'danger',
        //     "Message d'erreur"
        // );
        // -----------------------


        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            // addFlash est une interface simple pour créer un message flash via le controller
            // le success correspond à une class bootstrap qui donnera un fond vert au message d'alert
            // car la class sera alert-{{label}} ( label étant le premier paramètre et message le deuxième )
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request){

        // en paramètre, la classe du formulaire ( AdType ) et l'annonce ($ad)
        $form = $this->createForm(AdType::class, $ad);

        // handleRequest -> gère la requête
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }


    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show($slug, Ad $ad){
        // public function show($slug, Ad $ad) -> Ad $ad : est un ParamConverter,
        // il va chercher une ad qui correspond au slug

        // je récupère l'annonce qui correspond au slug !
        // $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }


}
