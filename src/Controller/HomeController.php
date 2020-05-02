<?php 

// les 3 pilliers pour une page : une fonction, une annotation route, une réponse

namespace App\Controller; // notre classe ne porte pas le nom HomeController mais le chemin donné du namespace

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {


    /** 
     * @Route("/bonjour/{prenom}/age/{age}", name="hello")
     * @Route("/bonjour", name="hello_base")
     * @Route("/bonjour/{prenom}", name="hello_prenom")
     * Route multiple
     * Il y a aussi des requirements, voir dans la doc symfony
     * Montre la page qui dit bonjour
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0) {
        // return new Response("Bonjour " . $prenom . " vous avez " . $age . " ans.");
        // Rappel : $this -> render() interprête un template twig sous forme d'une response
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age
            ]
            );
    }


    // on détermine le route qui appelle la fonction
    /**
     * @Route("/", name="homepage")
     */
    public function home(){
        // pour que symfony puisse instancier le homecontroller et appeler une fonction dessus
        // retourne une réponse
        // return new Response("
        // <html>
        //     <head>
        //         <title>Mon application</title>
        //     </head>
        //     <body>
        //         <h1>Bonjour à tous</h1>
        //         <p>C'est ma première page Symfony</p>
        //     </body>
        // </html>
        // ");

        $prenoms = ["Lior" => 31, "Joseph" => 12, "Anne" => 55];

        return $this->render(
            'home.html.twig',
            [ 
                'title' => 'Bonjour les amis !',
                'age' => 8,
                'tableau' => $prenoms
            ]
        );
    }
}

?>