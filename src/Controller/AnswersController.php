<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Repository\AnswersRepository;
use App\Repository\QuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnswersController extends AbstractController
{
    private array $errors = [];
    public function __construct(
        private readonly AnswersRepository $answersRepository,
        private readonly ValidatorInterface $validator,
        private readonly QuestionsRepository $questionsRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(path: '/answers/{id}', name: 'answer_read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readAnswer(int $id, Request $request): JsonResponse
    {
        $answer = $this->answersRepository->find($id);
        if($answer === null) {
            throw new NotFoundHttpException('Answer not found');
        }
        $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions/{id}/answers', name: 'question_answer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function addAnswerToQuestion(int $id, Request $request): JsonResponse
    {
        $question = $this->questionsRepository->find($id);
        if($question === null) {
            throw new NotFoundHttpException('Question not found');
        }
        $data = json_decode($request->getContent(), true);
        if (!isset($data['channel'], $data['body'])) {
            return new JsonResponse(['error' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
        }
        $answer = new Answers(
            $question,
            $data['channel'],
            $data['body'],
        );
        $errors = $this->validator->validate($answer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['error' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        $question->addAnswer($answer);
        $this->answersRepository->save($answer, true);
        $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/answers/{id}', name: 'answer_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateAnswerToQuestion(int $id, Request $request): JsonResponse
    {
        if (!isset($data['channel'], $data['body'])) {
            return new JsonResponse(['error' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
        }

        $answer = $this->answersRepository->find($id);
        if($answer === null) {
            throw new NotFoundHttpException('Answer not found');
        }
        $answerUpdated =
            $this->serializer->deserialize(
                $request->getContent(),
                $answer::class,
                'json',
                ['object_to_populate' => $answer]
            );
        $errors = $this->validator->validate($answerUpdated);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['error' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        $this->answersRepository->save($answerUpdated, true);
        $json = $this->serializer->serialize($answerUpdated, 'json', ['groups' => 'Answers:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);

    }

    #[Route(path: '/answers/{id}', name: 'answer_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteAnswer(int $id, Request $request): JsonResponse
    {

        $answer = $this->answersRepository->find($id);
        if($answer === null) {
            throw new NotFoundHttpException('Answer not found');
        }
        $this->answersRepository->remove($answer, true);
        return new JsonResponse(['success' => 'answer deleted'], Response::HTTP_OK);

    }

    #[Route(path: '/answers/export', name: 'exportAnswers', methods: ['GET'])]
    public function exportAnswers(): JsonResponse
    {
        return new JsonResponse($this->exportServices->formatContentToCsv('App\Entity\Answers'), Response::HTTP_OK);
    }

}
