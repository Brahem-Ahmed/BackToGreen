<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SingleGroupPerUserConstraint extends Constraint
{
    public string $message = 'This user already belongs to a group.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return SingleGroupPerUserConstraintValidator::class;
    }
}
