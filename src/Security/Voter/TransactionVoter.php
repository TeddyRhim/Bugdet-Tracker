<?php
/*  Mise en place d'un Voter abandonné. Raison : ma logique de base ne correspond pas avec l'application d'un Voter actuellement, à voir pour les autres use case.*/
namespace App\Security\Voter;

use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TransactionVoter extends Voter
{
    public const VIEW = 'TRANSACTION_VIEW';
    public const EDIT = 'TRANSACTION_EDIT';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::VIEW,
            self::EDIT,
        ]) && $subject instanceof Transaction;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = $subject;

        // ADMIN override
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $transaction->getUser() === $user,
            self::EDIT => $transaction->getUser() === $user,
            default => false,
        };
    }
}