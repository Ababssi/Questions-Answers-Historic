<?php

declare(strict_types=1);

namespace App\UpdateAnswer;

use App\Entity\Answers;
use App\Entity\Enum\AnswersChannel;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateAnswerCommand
{
    public function __construct(
        #[Assert\NotNull(message: 'Answer should not be null')]
        public Answers $answer,

        #[Assert\NotBlank(message: 'Channel should not be blank')]
        #[Assert\Choice(callback: [AnswersChannel::class, 'availableChannelsValues'])]
        public string $channel,

        #[Assert\NotBlank(message: 'Body should not be blank')]
        #[Assert\Length(max: 1000)] // example de constraint for length max
        public string $body,
    ) {
    }
}