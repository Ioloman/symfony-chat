<?php

namespace App\DataFixtures;

use App\Factory\ChatroomFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        UserFactory::createMany(20);
        UserFactory::new()->adminUsers()->create();

        ChatroomFactory::createMany(
            30,
            function() {
                $user = UserFactory::random();
                return [
                    'host' => $user,
                    'users' => [$user, UserFactory::random()],
                ];
            }
        );
    }
}
