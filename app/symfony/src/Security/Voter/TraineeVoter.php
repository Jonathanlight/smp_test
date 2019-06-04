<?php

namespace App\Security\Voter;

use App\Entity\Trainee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TraineeVoter extends Voter
{
    const TRAINEE_EDIT = 'traine_edit';

    protected $attributes = [
        self::TRAINEE_EDIT,
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
            case self::TRAINEE_EDIT:
                return $this->canEdit($subject, $user);
            default:
                return false;
        }
    }

    /**
     * @param Trainee $trainee
     *
     * @return bool
     */
    public function canEdit(Trainee $trainee, UserInterface $user): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            && $trainee->getOrder()->getCourse()->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }
}
