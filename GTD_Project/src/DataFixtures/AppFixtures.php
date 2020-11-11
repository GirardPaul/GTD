<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $genre1 = new Genre();
        $genre1->setLabel("Garçon");
        $manager->persist($genre1);

        $genre2 = new Genre();
        $genre2->setLabel("Fille");
        $manager->persist($genre2);

        $rand = rand(30, 50);

        for($i=1; $i < $rand; $i++)
        {
            $user = new Users();
            $user->setRoles("ROLE_USER")
                ->setPassword("password")
                ->setPasswordVerification("password")
                ->setFavoritePokemon($faker->word)
                ->setGenre($genre1)
                ->setUsername($faker->firstName);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
