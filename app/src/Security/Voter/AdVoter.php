<?php

/**
 * Ad voter.
 */

namespace App\Security\Voter;

use App\Entity\Ad;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AdVoter.
 */
final class AdVoter extends Voter
{
    public const CREATE = 'AD_CREATE';
    public const DELETE = 'AD_DELETE';
    public const EDIT = 'AD_EDIT';
    public const VIEW = 'AD_VIEW';

    /**
     * Constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::CREATE === $attribute) {
            return true;
        }

        return in_array(
            $attribute,
            [self::VIEW, self::EDIT, self::DELETE],
            true
        ) && $subject instanceof Ad;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute An attribute
     * @param mixed          $subject   The subject to secure
     * @param TokenInterface $token     A TokenInterface instance
     * @param Vote|null      $vote      The vote object
     *
     * @return bool True if the vote is granted, false otherwise
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (self::CREATE === $attribute) {
            return true;
        }

        if (!$subject instanceof Ad) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$subject->getVerified()) {
            return false;
        }

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => true,
            self::EDIT => ($user instanceof UserInterface) && $this->canEdit($subject, $user),
            self::DELETE => ($user instanceof UserInterface) && $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit the ad.
     *
     * @param Ad            $ad   Ad entity
     * @param UserInterface $user User interface
     *
     * @return bool True if user can edit, false otherwise
     */
    private function canEdit(Ad $ad, UserInterface $user): bool
    {
        return $ad->getAuthor() === $user;
    }

    /**
     * Checks if user can delete the ad.
     *
     * @param Ad            $ad   Ad entity
     * @param UserInterface $user User interface
     *
     * @return bool True if user can delete, false otherwise
     */
    private function canDelete(Ad $ad, UserInterface $user): bool
    {
        return $ad->getAuthor() === $user;
    }
}
