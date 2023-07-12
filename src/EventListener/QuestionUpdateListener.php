<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Questions;
use App\Message\TitleOrStatusQuestionUpdated;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Questions::class)]
readonly class QuestionUpdateListener
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }
    public function preUpdate(Questions $question, PreUpdateEventArgs $event): void
    {
        if (($event->hasChangedField('status')) || $event->hasChangedField('title')) {
            $oldStatus = $event->hasChangedField('status') ? $event->getOldValue('status') : $question->status();
            $oldTitle = $event->hasChangedField('title') ? $event->getOldValue('title') : $question->title();

            $questionUpdated = new TitleOrStatusQuestionUpdated(
                $question->id(),
                $oldTitle,
                $oldStatus,
            );
            $this->messageBus->dispatch($questionUpdated);
        }
    }
}
