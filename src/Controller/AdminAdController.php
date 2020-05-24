<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {
        // REQUIREMENTS
        // {page<\d+>} ce qu'il y a entre <> est les requirement, ici je dis via une regex 
        // que ce qu'il doit y avoir dans page est un nombre, si je veux le rentre optionnel
        // je mets un "?", exemple : {page<\d+>?}
        // si je veux rajouter la valeur par défaut je dois la mettre après le "?" {page<\d+>?1}

        #region Explication des différents Finds
        // Méthode find : permet de retrouver un enregistrement par son identifiant
        // $ad = $repo->find(382);
        // dump($ad);

        // $ad = $repo->findOneBy([
        //     'id' => 383,
        //     'title' => 'Quae voluptate dicta et ea magni reprehenderit.'
        // ]);
        // dump($ad);

        // $ad = $repo->findBy([], [], 5, 0);
        // dump($ad);
        #endregion


        $pagination->setEntityClass(Ad::class)
                   ->setPage($page)
                   ;

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée."
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager){
        if(count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations !"
            );
        } else{
            $manager->remove($ad);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimé !"
            );
        }
        return $this->redirectToRoute('admin_ads_index');
    }
}
