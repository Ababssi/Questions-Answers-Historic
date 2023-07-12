<?php

namespace App\Entity;

use App\Repository\HistoricQuestionRepository;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoricQuestionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class HistoricQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['HistoricQuestion:read','export'])]
    private int $id;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    #[Groups(['HistoricQuestion:read','export'])]
    private string $title;

    #[ORM\Column]
    #[Groups(['HistoricQuestion:read','export'])]
    private string $status;

    #[ORM\Column]
    #[Groups(['HistoricQuestion:read','export'])]
    private DateTimeImmutable $createAt;

    #[ORM\ManyToOne(inversedBy: 'historicQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private Questions $question;

    public function __construct(
        Questions $question,
        string $title,
        string $status,
    ) {
        $this->question = $question;
        $this->status = $status;
        $this->title = $title;
        $this->createAt = new DateTimeImmutable('now');
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreateAt(): DateTimeImmutable
    {
        return $this->createAt;
    }

    /**
     * @return Questions
     */
    public function getQuestion(): Questions
    {
        return $this->question;
    }

    public function setQuestion(Questions $question): static
    {
        $this->question = $question;

        return $this;
    }
}
