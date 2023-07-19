<?php

declare(strict_types=1);

namespace App\Tests\units\Export;

use App\Entity\Answers;
use App\Entity\HistoricQuestion;
use App\Entity\Questions;
use App\Services\ExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityToCsvTest extends WebTestCase
{
    private ExportService $exportService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();
        $this->exportService = $container->get(ExportService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->entityManager->getConnection()->setNestTransactionsWithSavepoints(true);
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->getConnection()->rollBack();

        parent::tearDown();
    }

    public function testExportOnQuestions(): void
    {
        $question1 = new Questions('Question 1', 'draft', false);
        $question2 = new Questions('Question 2', 'published', true);
        $question3 = new Questions('Question 3', 'draft', false);
        $question4 = new Questions('Question 4', 'published', false);

        $this->entityManager->persist($question1);
        $this->entityManager->persist($question2);
        $this->entityManager->persist($question3);
        $this->entityManager->persist($question4);
        $this->entityManager->flush();

        $questionsCreated = [
            [],
            ['title' => 'Question 1', 'status' => 'draft', 'promoted' => 'false'],
            ['title' => 'Question 2', 'status' => 'published', 'promoted' => 'true'],
            ['title' => 'Question 3', 'status' => 'draft', 'promoted' => 'false'],
            ['title' => 'Question 4', 'status' => 'published', 'promoted' => 'false']
        ];

        $result = $this->exportService->formatContent(Questions::class, 'csv');

        $this->assertEquals($result['file'], 'Questions.csv');

        $fileName = '/var/www/exportFiles/Questions.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 6) {
            $line = fgetcsv($file);
            if ($index > 0) { // sauter len-tete
                $question = [
                    'title' => $line[1],
                    'status' => $line[3],
                    'promoted' => $line[2]
                ];
                $this->assertEquals($question, [
                    'title' => $questionsCreated[$index]['title'],
                    'status' => $questionsCreated[$index]['status'],
                    'promoted' => $questionsCreated[$index]['promoted']
                ]);
            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }

    public function testExportOnAnswers(): void
    {
        $question1 = new Questions('Question 1', 'draft', false);

        $this->entityManager->persist($question1);
        $this->entityManager->flush();

        $answer1 = new Answers($question1, 'channel1', 'body1');
        $answer2 = new Answers($question1, 'channel2', 'body2');

        $this->entityManager->persist($answer1);
        $this->entityManager->persist($answer2);
        $this->entityManager->flush();

        $answersCreated = [
            [],
            ['channel' => 'channel1', 'body' => 'body1'],
            ['channel' => 'channel2', 'body' => 'body2']
        ];

        $result = $this->exportService->formatContent(Answers::class, 'csv');

        $this->assertEquals($result['file'], 'Answers.csv');

        $fileName = '/var/www/exportFiles/Answers.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 4) {
            $line = fgetcsv($file);
            if ($index > 0) { // skip header
                $answer = [
                    'channel' => $line[1],
                    'body' => $line[2],
                ];
                $this->assertEquals($answer, [
                    'channel' => $answersCreated[$index]['channel'],
                    'body' => $answersCreated[$index]['body']
                ]);

            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }
    public function testExportOnHistoricQuestions(): void
    {
        $question1 = new Questions('Question 1', 'draft', false);

        $this->entityManager->persist($question1);
        $this->entityManager->flush();

        $historicQuestion1 = new HistoricQuestion($question1, 'Historic Question 1', 'draft');
        $historicQuestion2 = new HistoricQuestion($question1, 'Historic Question 2', 'published');

        $this->entityManager->persist($historicQuestion1);
        $this->entityManager->persist($historicQuestion2);
        $this->entityManager->flush();

        $historicQuestionsCreated = [
            [],
            ['title' => 'Historic Question 1', 'status' => 'draft'],
            ['title' => 'Historic Question 2', 'status' => 'published']
        ];

        $result = $this->exportService->formatContent(HistoricQuestion::class, 'csv');

        $this->assertEquals($result['file'], 'HistoricQuestion.csv');

        $fileName = '/var/www/exportFiles/HistoricQuestion.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 4) {
            $line = fgetcsv($file);
            if ($index > 0) { // skip header
                $historicQuestion = [
                    'title' => $line[1],
                    'status' => $line[2]
                ];
                $this->assertEquals($historicQuestion, [
                    'title' => $historicQuestionsCreated[$index]['title'],
                    'status' => $historicQuestionsCreated[$index]['status']
                ]);

            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }

}
