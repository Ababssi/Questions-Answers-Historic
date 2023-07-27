<?php
declare(strict_types=1);
namespace App\CreateAnswer;

use App\Entity\Enum\AnswersChannel;
use App\Entity\Questions;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateAnswerCommand
{
    public function __construct(
        #[Assert\NotNull(message: 'Question should not be null')]
        public Questions $question,

        #[Assert\NotBlank(message: 'Channel should not be blank')]
        #[Assert\Choice(callback: [AnswersChannel::class, 'availableChannelsValues'])]
        public ?string $channel,

        #[Assert\NotBlank(message: 'Body should not be blank')]
        #[Assert\Length(max: 1000)] // example de constraint for length max
        public ?string $body,
    ) {
    }
}