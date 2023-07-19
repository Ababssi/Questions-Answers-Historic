<?php

namespace App\Entity;

use App\Entity\Enum\AnswersChannel;
use App\Repository\AnswersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnswersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Answers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Answers:read','Questions:read','export'])]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [AnswersChannel::class, 'availableChannelsValues'])]
    #[ORM\Column]
    #[Groups(['Answers:read','Questions:read','export'])]
    private string $channel;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['Answers:read','Questions:read','export'])]
    private string $body;

    #[ORM\Column]
    #[Groups(['export'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['export'])]
    private \DateTimeImmutable $updatedAt;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private Questions $question;

    public function __construct(
        Questions $question,
        string $channel,
        string $body,
    ) {
        $this->question = $question;
        $this->channel = $channel;
        $this->body = $body;
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function question(): Questions
    {
        return $this->question;
    }

    /**
     * @param Questions $question
     */
    public function setQuestion(Questions $question): void
    {
        $this->question = $question;
    }

    public function channel(): string
    {
        return $this->channel;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
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
}
