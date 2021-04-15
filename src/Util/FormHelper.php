<?php

namespace App\Util;

use App\Validator\Constraints\Base64ConstraintValidator;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FormHelper
 *
 * The form helper contains helpful methods
 * used in working with validation and user input.
 *
 * @package App\Util
 */
class FormHelper
{
    //todo add more
    const MISSING_CREDENTIALS = "Missing credentials!";
    const UNAUTHORIZED = "Unauthorized";
    const META_DELETED = "deleted";
    const META_SUCCESS = "success";
    const META_ERROR = "error";
    const META_INVALID = "Invalid data";
    const META_UPDATED = "updated";

    /**
     * Checks the expected form data with the current
     * and validates if all elements are in.
     *
     *
     * @param array $expected array of form data
     * @param array $current array of form data
     * @return bool
     */
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
     * The method validates the given object with its validation constraints .
     *
     * @param ValidatorInterface $validator
     * @param $object
     * @return array|bool array of error messages or true
     */
    public function validate(ValidatorInterface $validator, $object): bool|array
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

    /**
     * Used to validate string if it valid base64.
     *
     * If it is used in some sort of form use the constraint over that method.
     * @see Base64ConstraintValidator
     *
     * @param string $data
     * @return bool
     */
    public function base64validate(string $data): bool
    {
        if (base64_encode(base64_decode($data, true)) === $data) {
           return true;
        } else {
            return false;
        }
    }
}
