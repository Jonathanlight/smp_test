<?php

namespace App\Manager;

use App\Entity\Submission;
use App\Repository\SubmissionRepository;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class SubmissionManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var SubmissionRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param MessageService         $messageService
     * @param SubmissionRepository   $testimonialRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MessageService $messageService,
        SubmissionRepository $submissionRepository
    ) {
        $this->em = $entityManager;
        $this->messageService = $messageService;
        $this->repository = $submissionRepository;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param Submission $submission
     */
    public function create(Submission $submission): void
    {
        $this->em->persist($submission);
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.submissionOk');
    }
}
