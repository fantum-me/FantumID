<?php

namespace App\Validator;

use App\Utils\ColorScheme;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidColorSchemeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidColorScheme) {
            throw new UnexpectedTypeException($constraint, ValidColorScheme::class);
        }

        if (null === $value || '' === $value) return;

        if (!is_string($value)) throw new UnexpectedValueException($value, 'string');
        if (ColorScheme::isValid($value)) return;

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
