<?php

namespace App\UpdateAnswer;

use App\Entity\Answers;
use App\Repository\AnswersRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateAnswerCommandHandler
{
    public function __construct(
        private AnswersRepository $answerRepository,
    ) {
    }
    public function __invoke(UpdateAnswerCommand $command): Answers
    {
        $UpdatedAnswer = $command->answer;
        $UpdatedAnswer->setBody($command->body);
        $UpdatedAnswer->setChannel($command->channel);
        $this->answerRepository->save($UpdatedAnswer, true);
        return $command->answer;
    }
}