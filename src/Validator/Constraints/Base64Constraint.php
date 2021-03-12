<?php


namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class Base64Constraint extends Constraint
{
    public string $message = 'The base64 string is invalid.';
}