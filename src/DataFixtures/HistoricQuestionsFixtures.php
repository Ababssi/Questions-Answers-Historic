<?php

namespace App\DataFixtures;

use App\Entity\HistoricQuestion;
use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HistoricQuestionsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $question = new Questions('question', 'draft', true);
        $manager->persist($question);
        for($i=1; $i<6; $i++){
            $historicQuestion = new HistoricQuestion($question, 'question'.$i.'title', 'draft');
            $manager->persist($historicQuestion);
            $question->addHistoricQuestion($historicQuestion);
        }
        $manager->flush();
    }
}
