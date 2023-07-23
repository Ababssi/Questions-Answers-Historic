<?php

namespace App\Controller;

use App\DTO\AnswerDTO;
use App\Entity\Answers;
use App\Entity\Questions;
use App\Form\AnswerType;
use App\Repository\AnswersRepository;
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

class AnswersController extends AbstractController
{
    private array $errors = [];
    public function __construct(
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
        $answerDTO = new AnswerDTO;
        $form = $this->createForm(AnswerType::class, $answerDTO);
        $form->submit($data);
        if ($form->isValid()) {
            $answer = new Answers($question, $answerDTO->channel,$answerDTO->body);
            $this->answersRepository->save($answer, true);
            $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route(path: '/answers/{id}', name: 'answer_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateAnswerToQuestion(Answers $answer, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $AnswerDTO = new AnswerDTO;
        $form = $this->createform(AnswerType::class, $AnswerDTO);
        $form->submit($data);
        if ($form->isValid()) {
            $answer->setChannel($AnswerDTO->channel);
            $answer->setBody($AnswerDTO->body);
            $this->answersRepository->save($answer, true);
            $json = $this->serializer->serialize($answer, 'json', ['groups' => 'Answers:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route(path: '/answers/{id}', name: 'answer_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteAnswer(Answers $answer, Request $request): JsonResponse
    {
        $this->answersRepository->remove($answer, true);
        return new JsonResponse(['success' => 'answer deleted'], Response::HTTP_OK);
    }
}
