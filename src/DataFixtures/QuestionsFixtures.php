<?php

namespace App\DataFixtures;

use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i=1; $i<6; $i++){
            $question = new Questions('question'.$i, 'draft', true);
            $manager->persist($question);
        }
        $manager->flush();
    }
}
