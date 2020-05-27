<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public function getStats() {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $comments = $this->getCommentsCount();
        $bookings = $this->getBookingsCount();

        // Fonction compact() permet de crée un tableau automatiquement en nommant les clés, la valeur sera la variable qui porte le même nom que la clé
        return compact('users', 'ads', 'bookings', 'comments');
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        // getSingleScalarResult() permet d'obtenir le résultat sous forum d'une variable scalaire simple
        // $users = $manager->createQuery('SELECT u FROM App\Entity\User u')->getResult();
        // getResult() récupère les résultats sous forme d'objets entité ( ici des objets User )
        // dump($users);
    }

    public function getAdsCount() {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsCount() {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsCount() {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getAdsStats($direction){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note ' . $direction
            
        )->setMaxResults(5)
        ->getResult();
    }

}