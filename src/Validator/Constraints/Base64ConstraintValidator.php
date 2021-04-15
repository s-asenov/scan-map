<?php


namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class Base64ConstraintValidator
 *
 * The class is called when the Base64Constraint is called.
 * The whole logic about the constraint is put in the validator class.
 *
 * @package App\Validator\Constraints
 */
class Base64ConstraintValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Base64Constraint) {
            throw new UnexpectedTypeException($constraint, Base64Constraint::class);
        }

        if (base64_encode(base64_decode($value, true)) !== $value) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}