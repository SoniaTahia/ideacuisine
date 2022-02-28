<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
            $admin = new User;
            $admin
            ->setName('admin')
            ->setFirstname('admin')
            ->setPhone('0000000000')
            ->setAdress('admin')
            ->setPostCode('00000')
            ->setTown('admin')
            ->setEmail('admin@localhost.com')
            ->setRole(["ROLE_ADMIN"])
            ->setPassword('')
            ->setIsVerified(true).
            $manager->persist($admin);

            $manager->flush();

    }

}

