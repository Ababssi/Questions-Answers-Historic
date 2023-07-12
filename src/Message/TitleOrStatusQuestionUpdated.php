<?php

namespace App\Message;

final readonly class TitleOrStatusQuestionUpdated
{
    public function __construct(
        private int $questionId,
        private string $title,
        private string $status,
    ) {
    }
    public function questionId(): int
    {
        return $this->questionId;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }
}
