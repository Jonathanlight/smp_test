<?php

namespace App\Security\Voter;

use App\Entity\Center;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReportingVoter extends Voter
{
    const REPORTING_VIEW = 'reporting_view';
    const REPORTING_ORDER = 'reporting_order';
    const REPORTING_CENTER = 'reporting_center';

    protected $attributes = [
        self::REPORTING_VIEW,
        self::REPORTING_ORDER,
        self::REPORTING_CENTER,
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
            case self::REPORTING_VIEW:
                return $this->canView();
            case self::REPORTING_ORDER:
                return $this->canReportOrder();
            case self::REPORTING_CENTER:
                return $this->canReportCenter($user);
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function canView(): bool
    {
        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @return bool
     */
    public function canReportOrder(): bool
    {
        return $this->canView();
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function canReportCenter(UserInterface $user): bool
    {
        if (!$user->getCenter() instanceof Center) {
            return false;
        }

        return $this->canView();
    }
}
