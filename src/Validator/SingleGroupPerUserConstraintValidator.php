<?php

namespace App\Validator;

use App\Entity\MembreGroupe;
use App\Entity\StatutMembre;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SingleGroupPerUserConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SingleGroupPerUserConstraint) {
            throw new UnexpectedTypeException($constraint, SingleGroupPerUserConstraint::class);
        }

        if (!$value instanceof MembreGroupe) {
            throw new UnexpectedTypeException($value, MembreGroupe::class);
        }

        $user = $value->getIdUser();
        if (!$user) {
            return;
        }

        $currentId = $value->getId();
        $targetGroup = $value->getIdGroupe();

        // Count active memberships (excluding the current record being validated)
        $activeMemberships = 0;
        
        foreach ($user->getMembreGroupes() as $membership) {
            // Skip the current record if it's an edit
            if ($currentId !== null && $membership->getId() === $currentId) {
                continue;
            }

            // Count only active memberships to a different group
            if ($membership->getStatut() === StatutMembre::MEMBRE_ACTIF) {
                if ($targetGroup === null || $membership->getIdGroupe()->getId() !== $targetGroup->getId()) {
                    $activeMemberships++;
                }
            }
        }

        // If user already has an active membership to another group, reject
        if ($activeMemberships > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('idUser')
                ->addViolation();
        }
    }
}
