<?php

/**
 * Ad voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use App\Entity\Ad;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AdVoter.
 */
final class AdVoter extends Voter
{
    /**
     * Delete permission.
     *
     * @var string
     */
    public const DELETE = 'AD_DELETE';

    /**
     * Edit permission.
     *
     * @var string
     */
    public const EDIT = 'AD_EDIT';

    /**
     * View permission.
     *
     * @var string
     */
    public const VIEW = 'AD_VIEW';

    /**
     * Determines if this voter supports the attribute and subject.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof Ad;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     * @param Vote|null      $vote      Vote object
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$subject instanceof Ad) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can delete ad.
     *
     * @param Ad          $ad Ad entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Ad $ad, UserInterface $user): bool
    {
        return $ad->getAuthor() === $user;
    }

    /**
     * Checks if user can edit ad.
     *
     * @param Ad          $ad ad entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Ad $ad, UserInterface $user): bool
    {
        return $ad->getAuthor() === $user;
    }

    /**
     * Checks if a user can view an ad.
     *
     * @param Ad          $ad Ad entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canView(Ad $ad, UserInterface $user): bool
    {
        return $ad->getAuthor() === $user;
    }
}
