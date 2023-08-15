<?php

namespace App\DataFixtures;

use App\Entity\Answers;
use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for($i = 0; $i < 4; $i++){
            $questionStatus = $faker->randomElement(['draft', 'published']);
            $question = new Questions($faker->sentence(), $questionStatus, $faker->boolean());
            $manager->persist($question);
            for($j = 0; $j < 4; $j++){
                $answerChannel = $faker->randomElement(['faq', 'bot']);
                $answer = new Answers($question, $answerChannel, $faker->paragraph());
                $manager->persist($answer);
                $question->addAnswer($answer);
            }
        }
        $manager->flush();
    }
}
