<?php

namespace App\Util;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FormHelper
 *
 * The form helper contains helpful methods
 * used for validating form data.
 *
 * @package App\Util
 */
class FormHelper
{
    const MISSING_CREDENTIALS = "Missing credentials!";
    const UNAUTHORIZED = "Unauthorized";
    const META_DELETED = "deleted";
    const META_SUCCESS = "success";
    const META_ERROR = "error";
    const META_INVALID = "Invalid data";

    public function __construct()
    {
        //todo if needs to get whole request
    }

    public function checkFormData(array $expected, array $current): bool
    {
        if (count($expected) !== count($current)) {
            return false;
        }

        foreach ($current as $key => $item) {
            if (is_array($item)) {
                foreach ($item as $elKey => $el) {
                    if (!in_array($elKey, $expected[$key])) {
                        return false;
                    }
                }
            } elseif (!in_array($key, $expected)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ValidatorInterface $validator
     * @param $object
     * @return array|bool
     */
    public function validate(ValidatorInterface $validator, $object)
    {
        $errors = $validator->validate($object);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $messages;
        }

        return true;
    }

    public function base64validate(string $data): bool
    {
        if (base64_encode(base64_decode($data, true)) === $data) {
           return true;
        } else {
            return false;
        }
    }
}
