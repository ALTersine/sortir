<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Enum\EtatSortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        foreach (EtatSortie::cases() as $etatEnum) {
            $etat = new Etat();
            $etat->setId($etatEnum->value);
            $etat->setLibelle($etatEnum->label());
            $manager->persist($etat);
            $this->addReference($etatEnum->value, $etat);
        }

        $manager->flush();
    }
}
