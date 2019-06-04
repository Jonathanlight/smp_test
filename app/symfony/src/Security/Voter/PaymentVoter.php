<?php

namespace App\Security\Voter;

use App\Entity\Center;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PaymentVoter extends Voter
{
    const PAYMENT_VIEW = 'payment_view';

    protected $attributes = [
        self::PAYMENT_VIEW,
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
            case self::PAYMENT_VIEW:
                return $this->canView($user);
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
        return $user->getCenter() instanceof Center;
    }
}
