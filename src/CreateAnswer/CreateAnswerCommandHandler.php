<?php

declare(strict_types=1);

namespace App\CreateAnswer;

use App\Entity\Answers;
use App\Repository\AnswersRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateAnswerCommandHandler
{

    public function __construct(
        private AnswersRepository $answersRepository,
    ) {
    }

    public function __invoke(CreateAnswerCommand $createAnswerCommand): Answers
    {
        $answer = new Answers(
            $createAnswerCommand->question,
            $createAnswerCommand->channel,
            $createAnswerCommand->body,
        );
        $this->answersRepository->save($answer, true);
        return $answer;
    }
}