<?php

namespace App\Controller;

use App\Common\ErrorsAwareTrait;
use App\CreateAnswer\CreateAnswerCommand;
use App\Entity\Answers;
use App\Entity\Questions;
use App\Repository\AnswersRepository;
use App\UpdateAnswer\UpdateAnswerCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnswersController extends AbstractController
{
    use ErrorsAwareTrait;
    private array $errors = [];
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly MessageBusInterface $messageBus,
        private readonly AnswersRepository $answersRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(path: '/answers/{id}', name: 'answer_read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readAnswer(Answers $answer, Request $request): JsonResponse
    {
        $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions/{id}/answers', name: 'question_answer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function addAnswerToQuestion(Questions $question, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $createAnswer = new CreateAnswerCommand($question, $data['channel'], $data['body']);
        $errors = $this->validator->validate($createAnswer);
        if (count($errors) > 0) {
            return self::returnJsonResponseErrors($errors);
        }
        $createdAnswer = $this->messageBus->dispatch($createAnswer);
        $json = $this->serializer->serialize($createdAnswer, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/answers/{id}', name: 'answer_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateAnswerToQuestion(Answers $answer, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $UpdateAnswer = new UpdateAnswerCommand($answer, $data['channel'], $data['body']);
        $errors = $this->validator->validate($UpdateAnswer);
        if (count($errors) > 0) {
            return self::returnJsonResponseErrors($errors);
        }
        $this->messageBus->dispatch($UpdateAnswer);
        $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/answers/{id}', name: 'answer_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteAnswer(Answers $answer, Request $request): JsonResponse
    {
        $this->answersRepository->remove($answer, true);
        return new JsonResponse(['success' => 'answer deleted'], Response::HTTP_OK);
    }
}
