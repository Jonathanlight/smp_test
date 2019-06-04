<?php

namespace App\Security\Voter;

use App\Entity\Center;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PlaceVoter extends Voter
{
    const PLACE_VIEW = 'place_view';
    const PLACE_LIST = 'place_list';
    const PLACE_EDIT = 'place_edit';
    const PLACE_DELETE = 'place_delete';

    protected $attributes = [
        self::PLACE_VIEW,
        self::PLACE_LIST,
        self::PLACE_EDIT,
        self::PLACE_DELETE,
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
        $place = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PLACE_VIEW:
                return $this->canView($user);
            case self::PLACE_LIST:
                return $this->canList();
            case self::PLACE_EDIT:
                return $this->canEdit($user, $place);
            case self::PLACE_DELETE:
                return $this->canDelete();
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
    public function canList(): bool
    {
        return $this->authChecker->isGranted('ROLE_SMP') ||
                $this->authChecker->isGranted('ROLE_CSSR') ||
                $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @return bool
     */
    public function canEdit($user, $place): bool
    {
        if ($user->getCenter() && $user->getCenter()->getId() === $place->getCenter()->getId() && User::ROLE_CSSR === $user->getRole()) {
            return $this->authChecker->isGranted('ROLE_CSSR');
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CSSR') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }
}
