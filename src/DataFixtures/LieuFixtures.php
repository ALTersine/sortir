<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 30; $i++) {
            $sortie = $this->getReference('sortie' . $i, Sortie::class);

            $lieu = new Lieu()
                ->setName($faker->city())
                ->setRue($faker->streetAddress())
                ->setCodePostal(
                    $this->getReference(
                        $sortie->getCampus()->getName() . ($faker->numberBetween(1, 10)), Ville::class
                    )
                );

            $manager->persist($lieu);
            $manager->flush();

            $sortie->addLieux($lieu);
            $manager->persist($sortie);
            $manager->flush();

        }
    }

    public function getDependencies(): array
    {
        return [VilleFixtures::class, SortieFixtures::class];
    }
}
