<?php

namespace App\Security\Voter;

use App\Entity\Center;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CenterVoter extends Voter
{
    const CENTER_VIEW = 'center_view';
    const CENTER_EDIT = 'center_edit';

    protected $attributes = [
        self::CENTER_VIEW,
        self::CENTER_EDIT,
    ];

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authChecker;

    /**
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, $this->attributes)) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CENTER_VIEW:
                return $this->canView($user);
            case self::CENTER_EDIT:
                return $this->canEdit($user, $subject);
            default:
                return false;
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canView(User $user): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')) {
            return $user->getCenter() instanceof Center;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    public function canCreate(): bool
    {
        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @param User   $user
     * @param Center $center
     *
     * @return bool
     */
    public function canEdit(User $user, ?Center $center): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')) {
            return $user->getCenter() === $center;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }
}
