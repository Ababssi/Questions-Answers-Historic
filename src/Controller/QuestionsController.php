<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\ErrorsAwareTrait;
use App\Dto\QuestionDTO;
use App\Entity\Questions;
use App\Form\QuestionType;
use App\Repository\QuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class QuestionsController extends AbstractController
{
    use ErrorsAwareTrait;
    public function __construct(
        private readonly QuestionsRepository        $questionsRepository,
        private readonly SerializerInterface        $serializer,
    ) {
    }

    #[Route(path: '/questions/{id}', name: 'question_read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readQuestion(Questions $question, Request $request): JsonResponse
    {
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
        $form = $this->createForm(QuestionType::class);
        $form->submit($data, false);
        if (!$form->isValid()) {
            return self::returnJsonResponseErrors($form->getErrors(true, false));
        }
        $questionDTO = $form->getData();
        $question = new Questions($questionDTO->title, $questionDTO->status, $questionDTO->promoted);
        $this->questionsRepository->save($question, true);
        $json = $this->serializer->serialize($question, 'json', ['groups' => 'Questions:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route(path: '/questions/{id}', name: 'question_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateQuestion(Questions $question, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm((QuestionType::class));
        $form->submit($data, false);
        if (!$form->isValid()) {
            return self::returnJsonResponseErrors($form->getErrors(true, false));
        }
        /** @var QuestionDTO $questionDTO */
        $questionDTO = $form->getData();
        $question->setTitle($questionDTO->title());
        $question->setStatus($questionDTO->status());
        $question->setPromoted($questionDTO->promoted());
        $this->questionsRepository->save($question, true);
        $json = $this->serializer->serialize($question, 'json', ['groups' => 'Questions:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/questions/{id}', name: 'question_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteQuestion(Questions $question, Request $request): JsonResponse
    {
        $this->questionsRepository->remove($question, true);
        return new JsonResponse(['success' => 'Question deleted'], Response::HTTP_OK);
    }
}
