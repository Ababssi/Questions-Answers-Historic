<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Questions;
use App\Repository\QuestionsRepository;
use App\Services\ExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuestionsController extends AbstractController
{
    private array $errors = [];
    public function __construct(
        private readonly ValidatorInterface         $validator,
        private readonly QuestionsRepository        $questionsRepository,
        private readonly SerializerInterface        $serializer,
        private readonly ExportService              $exportServices,
    ) {
    }

    #[Route(path: '/questions/{id}', name: 'question_read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readQuestion(int $id, Request $request): JsonResponse
    {
        $question = $this->questionsRepository->find($id);
        if($question === null) {
            throw new NotFoundHttpException('Question not found');
        }
        $json = $this->serializer->serialize($question, 'json', ['groups' => 'Questions:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions', name: 'questions_list', methods: ['GET'])]
    public function listAllQuestion(Request $request): JsonResponse
    {
        $questions = $this->questionsRepository->findAll();
        $questionsNormalized = [];
        foreach ($questions as $question) {
            $questionsNormalized[] = $this->serializer->normalize($question, 'json', ['groups' => 'Questions:read']);
        }
        return new JsonResponse($questionsNormalized, Response::HTTP_OK);
    }

    #[Route(path: '/questions', name: 'question_create', methods: ['POST'])]
    public function createQuestion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['title'], $data['status'], $data['promoted'])) {
            return new JsonResponse(['error' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
        }
        if(!is_bool($data['promoted'])) {
            return new JsonResponse(['error' => 'Promoted must be a boolean'], Response::HTTP_BAD_REQUEST);
        }
        $question = new Questions(
            $data['title'],
            $data['status'],
            $data['promoted'],
        );

        $errors = $this->validator->validate($question);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        $this->questionsRepository->save($question, true);
        $json = $this->serializer->serialize($question, 'json', ['groups' => 'Questions:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions/{id}', name: 'question_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateQuestion(int $id, Request $request): JsonResponse
    {
        $question = $this->questionsRepository->find($id);
        if ($question === null) {
            throw new NotFoundHttpException('Question not found');
        }
        $data = json_decode($request->getContent(), true);
        if (!isset($data['title'], $data['status'], $data['promoted'])) {
            return new JsonResponse(['error' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
        }
        if(!is_bool($data['promoted'])) {
            return new JsonResponse(['error' => 'Promoted must be a boolean'], Response::HTTP_BAD_REQUEST);
        }
        $questionUpdated = $this->serializer->deserialize(
            $request->getContent(),
            $question::class,
            'json',
            ['object_to_populate' => $question]
        );
        $errors = $this->validator->validate($questionUpdated);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        $this->questionsRepository->save($questionUpdated, true);
        $json = $this->serializer->serialize($questionUpdated, 'json', ['groups' => 'Questions:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions/{id}', name: 'question_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteQuestion(int $id, Request $request): JsonResponse
    {

        $question = $this->questionsRepository->find($id);
        if($question === null) {
            throw new NotFoundHttpException('Question not found');
        }
        $this->questionsRepository->remove($question, true);
        return new JsonResponse(['success' => 'Question deleted'], Response::HTTP_OK);

    }

    #[Route(path: '/historicQuestion/export', name: 'historicQuestionExport', methods: ['GET'])]
    public function historicQuestionExport(): JsonResponse
    {
        return new JsonResponse($this->exportServices->formatContentToCsv('App\Entity\HistoricQuestion'), Response::HTTP_OK);
    }

    #[Route(path: '/questions/export', name: 'exportQuestion', methods: ['GET'])]
    public function exportQuestion(): JsonResponse
    {
        return new JsonResponse($this->exportServices->formatContentToCsv('App\Entity\Questions'), Response::HTTP_OK);
    }

}
