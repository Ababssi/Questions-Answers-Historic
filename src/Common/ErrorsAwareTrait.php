<?php

namespace App\Common;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use function PHPUnit\Framework\isInstanceOf;

trait ErrorsAwareTrait
{
    public function returnJsonResponseErrors(FormErrorIterator|ConstraintViolationListInterface $errors): JsonResponse
    {
        if ($errors instanceof FormErrorIterator)
        {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }
        else
        {
            $errorsTab = [];
            foreach ($errors as $error) {
                $errorsTab[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorsTab], Response::HTTP_BAD_REQUEST);
        }
    }
}