<?php

/**
 * Topic voter.
 */

namespace App\Security\Voter;

use App\Entity\Topic;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TopicVoter.
 */
final class TopicVoter extends Voter
{
    public const CREATE = 'TOPIC_CREATE';
    public const DELETE = 'TOPIC_DELETE';
    public const EDIT = 'TOPIC_EDIT';
    public const VIEW = 'TOPIC_VIEW';

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

        return in_array($attribute, [self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof Topic;
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
            return $token->getUser() instanceof UserInterface;
        }

        if (in_array($attribute, [self::VIEW])) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Topic) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can delete the topic.
     *
     * @param Topic         $topic Topic entity
     * @param UserInterface $user  User interface
     *
     * @return bool True if user can delete, false otherwise
     */
    private function canDelete(Topic $topic, UserInterface $user): bool
    {
        return $topic->getAuthor() === $user;
    }

    /**
     * Checks if user can edit the topic.
     *
     * @param Topic         $topic Topic entity
     * @param UserInterface $user  User interface
     *
     * @return bool True if user can edit, false otherwise
     */
    private function canEdit(Topic $topic, UserInterface $user): bool
    {
        return $topic->getAuthor() === $user;
    }
}
