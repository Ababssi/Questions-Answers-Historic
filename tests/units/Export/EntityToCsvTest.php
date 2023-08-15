<?php

declare(strict_types=1);

namespace App\Tests\units\Export;

use App\DataFixtures\AnswersFixtures;
use App\DataFixtures\HistoricQuestionsFixtures;
use App\DataFixtures\QuestionsFixtures;
use App\Entity\Answers;
use App\Entity\HistoricQuestion;
use App\Entity\Questions;
use App\Services\ExportService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityToCsvTest extends WebTestCase
{
    private ExportService $exportService;
    private EntityManagerInterface $entityManager;
    const PATH_DOCKER = '/var/www/exportFiles/';
    const PATH_LOCAL = 'exportFiles/';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();
        $this->exportService = $container->get(ExportService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get();

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
        $this->databaseTool->loadFixtures([QuestionsFixtures::class]);
        $result = $this->exportService->formatContent(Questions::class, 'csv');

        $this->assertEquals($result['file'], 'Questions.csv');

        $fileName = self::PATH_LOCAL.'Questions.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 6) {
            $line = fgetcsv($file);
            if ($index > 0) { // skip header
                $this->assertEquals([$line[1], $line[3], $line[2]],
                    ['question'.$index, 'draft', 'true']
                );
            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }

    public function testExportOnAnswers(): void
    {
        $this->databaseTool->loadFixtures([AnswersFixtures::class]);
        $result = $this->exportService->formatContent(Answers::class, 'csv');

        $this->assertEquals($result['file'], 'Answers.csv');

        $fileName = self::PATH_LOCAL.'Answers.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 6) {
            $line = fgetcsv($file);
            if ($index > 0) { // skip header
                $this->assertEquals([$line[1], $line[2]],
                    ['faq', 'answer'.$index]
                );
            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }

    public function testExportOnHistoricQuestions(): void
    {
        $this->databaseTool->loadFixtures([HistoricQuestionsFixtures::class]);
        $result = $this->exportService->formatContent(HistoricQuestion::class, 'csv');

        $this->assertEquals($result['file'], 'HistoricQuestion.csv');

        $fileName = self::PATH_LOCAL.'HistoricQuestion.csv';
        $file = fopen($fileName, 'r');
        $index = 0; // file line
        while ($index < 6) {
            $line = fgetcsv($file);
            if ($index > 0) { // skip header
                $this->assertEquals([$line[1], $line[2]], ['question'.$index.'title', 'draft']
                );
            }
            $index++;
        }
        fclose($file);
        unlink($fileName);
    }
}
