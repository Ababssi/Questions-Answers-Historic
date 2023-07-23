<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Enum\QuestionStatus;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionDTO
{
    #[Assert\NotBlank(message: 'Title should not be blank')]
    #[Assert\Length(max: 100)]
    public ?string $title;

    #[Assert\NotBlank(message: 'Promoted should not be blank')]
    #[Assert\Type(type: 'boolean')]
    public ?bool $promoted;

    #[Assert\NotBlank(message: 'Status should not be blank')]
    #[Assert\Choice(callback: [QuestionStatus::class, 'availableStatusesValues'])]
    public ?string $status;

    /**
     * @return string|null
     */
    public function title(): ?string
    {
        return $this->title;
    }

    /**
     * @return bool|null
     */
    public function promoted(): ?bool
    {
        return $this->promoted;
    }

    /**
     * @return string|null
     */
    public function status(): ?string
    {
        return $this->status;
    }
}
