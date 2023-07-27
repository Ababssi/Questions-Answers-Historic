<?php

namespace App\Entity;

use App\Entity\Enum\QuestionStatus;
use App\Repository\QuestionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: QuestionsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Questions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Questions:read','export'])]
    private int $id;

    #[Assert\NotBlank(message: 'title should not be blank')]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    #[Groups(['Questions:read','export'])]
    private string $title;

    #[Assert\NotNull(message: 'promoted should not be null')]
    #[ORM\Column]
    #[Groups(['Questions:read','export'])]
    private bool $promoted;

    #[Assert\NotBlank(message: 'status should not be blank')]
    #[Assert\Choice(callback: [QuestionStatus::class, 'availableStatusesValues'])]
    #[ORM\Column]
    #[Groups(['Questions:read','export'])]
    private string $status;

    #[Groups(['export'])]
    #[ORM\Column]
    private \DateTimeImmutable $createAt;

    #[Groups(['export'])]
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[Groups(['Questions:read'])]
    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answers::class, cascade: ['remove'])]
    private Collection $answers;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: HistoricQuestion::class, orphanRemoval: true)]
    private Collection $historicQuestions;

    public function __construct(
        string $title,
        string $status,
        bool $promoted,
    ) {
        $this->title = $title;
        $this->promoted = $promoted;
        $this->status = $status;
        $this->answers = new ArrayCollection();
        $this->createAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTimeImmutable('now');
        $this->historicQuestions = new ArrayCollection();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function promoted(): bool
    {
        return $this->promoted;
    }

    public function setPromoted(bool $promoted): static
    {
        $this->promoted = $promoted;

        return $this;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreateAt(): DateTimeImmutable
    {
        return $this->createAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PostUpdate]
    #[ORM\PostPersist]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    /**
     * @return Collection<int, Answers>
     */
    public function answers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answers $answer): void
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }
    }

    /**
     * @return Collection<int, HistoricQuestion>
     */
    public function getHistoricQuestions(): Collection
    {
        return $this->historicQuestions;
    }

    public function addHistoricQuestion(HistoricQuestion $historicQuestion): static
    {
        if (!$this->historicQuestions->contains($historicQuestion)) {
            $this->historicQuestions->add($historicQuestion);
            $historicQuestion->setQuestion($this);
        }
        return $this;
    }
}
