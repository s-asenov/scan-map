<?php


namespace App\Util;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

class ApiRequest extends Request
{
    /**
     * Call the method to validate the custom data
     * with the given constraint collection.
     *
     * @param Assert\Collection $constraint
     * @param Assert\GroupSequence|null $groups
     * @return void|JsonResponse void if there are not errors otherwise return json response
     */
    public function validate(Assert\Collection $constraint, Assert\GroupSequence $groups = null)
    {
        $data = $this->toArray();
        $validator = Validation::createValidator();

        $violations = $validator->validate($data, $constraint, $groups);

        $errors = [];

        foreach ($violations as $violation) {
            //Every property path is wrapped in brackets and we must remove them.
            //Ex. `[item]` to `item`
            $property = substr($violation->getPropertyPath(), 1, -1);

            $errors[$property] = $violation->getMessage();
        }


        if (!empty($errors)) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }
    }
}