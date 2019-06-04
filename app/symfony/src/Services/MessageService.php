<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MessageService
{
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    /**
     * @var FlashBagInterface
     */
    protected $flashBag;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param FlashBagInterface   $flashBag
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    /**
     * @param string $message
     */
    public function addSuccess(string $message): void
    {
        $this->flashBag->add(self::TYPE_SUCCESS, $this->translator->trans($message));
    }

    /**
     * @param string $message
     */
    public function addError(string $message): void
    {
        $this->flashBag->add(self::TYPE_ERROR, $this->translator->trans($message));
    }
}
