<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $type = new Type();
        $type->setName('info');
        $manager->persist($type);

        $type = new Type();
        $type->setName('warning');
        $manager->persist($type);

        $type = new Type();
        $type->setName('error');
        $manager->persist($type);

        $manager->flush();
    }
}
