<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Command;

class CommandFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 16; $i++) {
            $command = new Command();
            $command->setName(
                'Team '. random_int(0, 1000)
            );
            $manager->persist($command);

        }
        $manager->flush();
    }
}
