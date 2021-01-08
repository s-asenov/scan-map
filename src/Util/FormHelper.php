<?php

namespace App\Util;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormHelper
{
    const MISSING_CREDENTIALS = "Missing credentials!";
    const UNAUTHORIZED = "Unauthorized";
    const META_DELETED = "deleted";
    const META_SUCCESS = "success";
    const META_ERROR = "error";

    public function __construct()
    {
        //todo if needs to get whole request
    }

    public function checkFormData(array $expected, array $current)
    {
        if (count($expected) !== count($current)) {
            return false;
        }

        foreach ($current as $key => $item) {
            if (!in_array($key, $expected)) {
                return false;
            }
        }

        return true;
    }

    public function validate($object)
    {
        $validate = Validation::createValidator();
        $errors = $validate->validate($object);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return $errorsString;
        }

//        return new Response('The author is valid! Yes!');
        return "ok";
    }

}
