<?php 

// les 3 pilliers pour une page : une fonction, une annotation route, une réponse

namespace App\Controller; // notre classe ne porte pas le nom HomeController mais le chemin donné du namespace

use App\Repository\AdRepository;
use App\Repository\UserRepository;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {


    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo){
        return $this->render(
            'home.html.twig', [
                'ads' => $adRepo->findBestAds(3),
                'users' => $userRepo->findBestUsers(2),
            ]
        );
    }
}

?>