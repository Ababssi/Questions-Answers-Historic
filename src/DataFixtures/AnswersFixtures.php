<?php

namespace App\DataFixtures;

use App\Entity\Answers;
use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AnswersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $question = new Questions('question', 'draft', true);
        $manager->persist($question);
        for($i=1; $i<6; $i++){
            $answer = new Answers($question, 'faq', 'answer'.$i);
            $manager->persist($answer);
            $question->addAnswer($answer);
        }

        $manager->flush();
    }
}
