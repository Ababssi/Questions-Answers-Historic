<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Enum\AnswersChannel;
use App\Entity\Questions;
use Symfony\Component\Validator\Constraints as Assert;

class AnswerDTO
{
        #[Assert\NotBlank(message: 'Channel should not be blank')]
        #[Assert\Choice(callback: [AnswersChannel::class, 'availableChannelsValues'])]
        public string $channel;

        #[Assert\NotBlank(message: 'Body should not be blank')]
        #[Assert\Length(max: 1000)] // example de constraint for length max
        public string $body;

    /**
     * @return string
     */
    public function channel(): string
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }



}