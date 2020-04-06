<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Training;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

// class AppFixtures extends Fixture
// {
//     public function userfixture(ObjectManager $manager)
//     {
//         for ($i = 0; $i < 20; $i++) {
//             $user = new User();
//             $user->setEmail("user$i@domain.fr");
//             $user->setRoles(["ROLE_STUDENT"]);
//             $user->setPassword("00000");
//             $user->setLastname("prenom". $i);
//             $user->setFirstname("nom". $i);
//             $user->setPhone("0622222222");
//             $user->setAddress("adresse");
//             $user->setPostcode("59");
//             $user->setCity("Lille");

//             $manager->persist($user);
//         }
//         $manager->flush();
//     }

    
// }
