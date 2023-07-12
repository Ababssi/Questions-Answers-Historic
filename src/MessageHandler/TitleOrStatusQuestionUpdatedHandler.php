<?php

namespace App\MessageHandler;

use App\Entity\HistoricQuestion;
use App\Message\TitleOrStatusQuestionUpdated;
use App\Repository\HistoricQuestionRepository;
use App\Repository\QuestionsRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class TitleOrStatusQuestionUpdatedHandler
{
    public function __construct(
        private HistoricQuestionRepository $historicQuestionRepository,
        private QuestionsRepository        $questionsRepository,
    ) {
    }
    public function __invoke(TitleOrStatusQuestionUpdated $message): void
    {
        $question = $this->questionsRepository->find($message->questionId());
        $historicQuestion = new HistoricQuestion(
            $question,
            $message->title(),
            $message->status(),
        );
        $this->historicQuestionRepository->save($historicQuestion, true);
    }
}
