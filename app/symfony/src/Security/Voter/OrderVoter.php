<?php

namespace App\Security\Voter;

use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    const ORDER_VIEW = 'order_view';
    const ORDER_LIST = 'order_list';

    const ORDER_WAIT = 'order_wait';
    const ORDER_TRANSFER = 'order_transfer';
    const ORDER_CANCEL = 'order_cancel';
    const ORDER_CONFIRM = 'order_confirm';
    const ORDER_REFUND = 'order_refund';

    protected $attributes = [
        self::ORDER_VIEW,
        self::ORDER_LIST,
        self::ORDER_TRANSFER,
        self::ORDER_WAIT,
        self::ORDER_CANCEL,
        self::ORDER_CONFIRM,
        self::ORDER_REFUND,
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
            case self::ORDER_VIEW:
                return $this->canView($user, $subject);
            case self::ORDER_LIST:
                return $this->canList();
            case self::ORDER_TRANSFER:
                return $this->canTransfer($user, $subject);
            case self::ORDER_CANCEL:
                return $this->canCancel($user, $subject);
            case self::ORDER_CONFIRM:
                return $this->canConfirm($user, $subject);
            case self::ORDER_REFUND:
                return $this->canRefund($user, $subject);
            case self::ORDER_WAIT:
                return $this->canWait($user, $subject);
            default:
                return false;
        }
    }

    /**
     * @param User  $user
     * @param Order $order
     *
     * @return bool
     */
    public function canView(User $user, Order $order): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            || $order->getCourse()->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @return bool
     */
    public function canList(): bool
    {
        return true;
    }

    /**
     * @param User  $user
     * @param Order $order
     *
     * @return bool
     */
    public function canEdit(User $user, Order $order): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            || $order->getCourse()->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @param User  $user
     * @param Order $order
     *
     * @return bool
     */
    public function canDelete(User $user, Order $order): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            || $order->getCourse()->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @param UserInterface $user
     * @param Order         $order
     *
     * @return bool
     */
    public function canTransfer(UserInterface $user, Order $order): bool
    {
        if ($order->getCourse()->getPayment() instanceof Payment) {
            return false;
        }

        if ($this->authChecker->isGranted('ROLE_CSSR')) {
            return $user->getCenter() === $order->getCourse()->getCenter()
                && null !== $order->getTrainee()->getReference();
        }

        return ($this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT'))
            && null !== $order->getTrainee()->getReference();
    }

    /**
     * @param UserInterface $user
     * @param Order         $order
     *
     * @return bool
     */
    public function canWait(UserInterface $user, Order $order): bool
    {
        return ($this->authChecker->isGranted('ROLE_SMP') ||
                $this->authChecker->isGranted('ROLE_CONSULTANT'))
            && null !== $order->getTrainee()->getReference();
    }

    /**
     * @param UserInterface $user
     * @param Order         $order
     *
     * @return bool
     */
    public function canRefund(UserInterface $user, Order $order): bool
    {
        if ($order->getCourse()->getPayment() instanceof Payment) {
            return false;
        }

        return ($this->authChecker->isGranted('ROLE_SMP') ||
                $this->authChecker->isGranted('ROLE_CONSULTANT'))
            && null !== $order->getTrainee()->getReference();
    }

    /**
     * @param UserInterface $user
     * @param Order         $order
     *
     * @return bool
     */
    public function canConfirm(UserInterface $user, Order $order): bool
    {
        return null !== $order->getTrainee()->getReference();
    }

    /**
     * @param UserInterface $user
     * @param Order         $order
     *
     * @return bool
     */
    public function canCancel(UserInterface $user, Order $order): bool
    {
        return null !== $order->getTrainee()->getReference();
    }
}
