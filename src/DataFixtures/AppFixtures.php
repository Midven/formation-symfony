<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR'); // faker en français


        // Création d'un compte administrateur
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Vincent')
                    ->setLastName('Midlaire')
                    ->setEmail('vincent.midlaire@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                    ->setPicture('https://randomuser.me/api/portraits/men/3.jpg')
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                    ->addUserRole($adminRole);
        $manager->persist($adminUser);

        $users = [];
        $genres = ['male', 'female'];
        
        // Nous gérons les utilisateurs
        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99) . '.jpg';
            $picture = $picture . ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
            
            $hash = $this->encoder->encodePassword($user, 'password');


            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }


        // Nous gérons les annonces
        for ($i=0; $i <= 30; $i++) {

            $ad = new Ad();
            $title = $faker->sentence();
            // sentence va générer une phrase en lorem
            $coverImage = $faker->imageUrl(1000, 350);
            // imageUrl va afficher une image aléatoire lorempixel avec les dimensions choisies
            $introduction = $faker->paragraph(2);
            // paragraph va générer un paragraph en lorem
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) -1 )];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(20, 400))
                ->setRooms(mt_rand(2, 5))
                ->setAuthor($user);
    

            // Pour chaque annonce je crée 2 à 5 images qui lui seront associée
            for ($j=0; $j < mt_rand(2,5); $j++) { 
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                    
                    $manager->persist($image);
            }

            $manager->persist($ad);
            // persist -> pour faire persister mon annonce ( ad )
            // persist prévient Doctrine qu'on veut sauver
        }

        $manager->flush();
        // flush envoie la requête finale
    }
}
