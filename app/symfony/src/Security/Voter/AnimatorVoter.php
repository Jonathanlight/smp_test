<?php

namespace App\Security\Voter;

use App\Entity\Center;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AnimatorVoter extends Voter
{
    const ANIMATOR_VIEW = 'animator_view';
    const ANIMATOR_LIST = 'animator_list';
    const ANIMATOR_EDIT = 'animator_edit';
    const ANIMATOR_DELETE = 'animator_delete';

    protected $attributes = [
        self::ANIMATOR_VIEW,
        self::ANIMATOR_LIST,
        self::ANIMATOR_EDIT,
        self::ANIMATOR_DELETE,
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
            case self::ANIMATOR_VIEW:
                return $this->canView($user);
            case self::ANIMATOR_LIST:
                return $this->canList($user);
            case self::ANIMATOR_EDIT:
                return $this->canEdit($user);
            case self::ANIMATOR_DELETE:
                return $this->canDelete($user);
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function canView($user): bool
    {
        return $user->getCenter() instanceof Center;
    }

    /**
     * @return bool
     */
    public function canList($user): bool
    {
        return $user->getCenter() instanceof Center;
    }

    /**
     * @return bool
     */
    public function canEdit($user): bool
    {
        return $user->getCenter() instanceof Center;
    }

    /**
     * @return bool
     */
    public function canDelete($user): bool
    {
        return $user->getCenter() instanceof Center;
    }
}
