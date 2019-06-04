<?php

namespace App\Security\Voter;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CourseVoter extends Voter
{
    const COURSE_VIEW = 'course_view';
    const COURSE_LIST = 'course_list';
    const COURSE_EDIT = 'course_edit';
    const COURSE_DELETE = 'course_delete';
    const COURSE_ENABLE = 'course_enable';
    const COURSE_DISABLE = 'course_disable';
    const COURSE_REGISTER = 'course_register';

    protected $attributes = [
        self::COURSE_VIEW,
        self::COURSE_LIST,
        self::COURSE_EDIT,
        self::COURSE_DELETE,
        self::COURSE_ENABLE,
        self::COURSE_DISABLE,
        self::COURSE_REGISTER,
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
            case self::COURSE_VIEW:
                return $this->canView($user, $subject);
            case self::COURSE_LIST:
                return $this->canList($user);
            case self::COURSE_EDIT:
                return $this->canEdit($user, $subject);
            case self::COURSE_DELETE:
                return $this->canDelete($subject);
            case self::COURSE_ENABLE:
                return $this->canEnable($user, $subject);
            case self::COURSE_DISABLE:
                return $this->canDisable($user, $subject);
            case self::COURSE_REGISTER:
                return $this->canRegister();
            default:
                return false;
        }
    }

    /**
     * @param User   $user
     * @param Course $course
     *
     * @return bool
     */
    public function canEnable(User $user, Course $course): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            && $course->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @param User   $user
     * @param Course $course
     *
     * @return bool
     */
    public function canDisable(User $user, Course $course): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            && $course->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @return bool
     */
    public function canView(User $user, Course $course): bool
    {
        if ($this->authChecker->isGranted('ROLE_CSSR')
            && $course->getCenter() === $user->getCenter()) {
            return true;
        }

        return $this->authChecker->isGranted('ROLE_SMP') ||
            $this->authChecker->isGranted('ROLE_CONSULTANT');
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canList(User $user): bool
    {
        return true;
    }

    /**
     * @param User   $user
     * @param Course $course
     *
     * @return bool
     */
    public function canEdit(User $user, ?Course $course): bool
    {
        if (!$course instanceof Course && $user->getCenter() instanceof Center) {
            return true;
        }

        if (!$course instanceof Course && !$user->getCenter() instanceof Center) {
            return false;
        }

        if (($this->authChecker->isGranted('ROLE_SMP')
            || $this->authChecker->isGranted('ROLE_CONSULTANT')) && $course && $course->getStartOn() > new \DateTime(date('Y-m-d'))) {
            return true;
        }

        if ($this->authChecker->isGranted('ROLE_CSSR')
            && $user->getCenter() instanceof Center
            && $course
            && $course->getStartOn() > new \DateTime(date('Y-m-d'))) {
            return $course instanceof Course || null === $course;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canRegister(): bool
    {
        return $this->authChecker->isGranted('ROLE_CSSR');
    }

    /**
     * @param Course $course
     *
     * @return bool
     */
    public function canDelete(Course $course): bool
    {
        return !$course->getOrders()->count();
    }
}
