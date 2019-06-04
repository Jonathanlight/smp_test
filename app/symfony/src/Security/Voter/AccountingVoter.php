<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountingVoter extends Voter
{
    const ACCOUNTING_VIEW = 'accounting_view';
    const ACCOUNTING_FEE = 'accounting_fee';
    const ACCOUNTING_REFUND = 'accounting_refund';
    const ACCOUNTING_BANKING = 'accounting_banking';

    protected $attributes = [
        self::ACCOUNTING_VIEW,
        self::ACCOUNTING_FEE,
        self::ACCOUNTING_REFUND,
        self::ACCOUNTING_BANKING,
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
            case self::ACCOUNTING_VIEW:
            case self::ACCOUNTING_FEE:
            case self::ACCOUNTING_REFUND:
            case self::ACCOUNTING_BANKING:
                return $this->canView();
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function canView()
    {
        return $this->authChecker->isGranted('ROLE_SMP');
    }
}
