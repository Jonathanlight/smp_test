<?php

namespace App\Controller\Middle;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\Order;
use App\Entity\Trainee;
use App\Form\CourseType;
use App\Form\Search\CourseSearchType;
use App\Form\TraineeType;
use App\Manager\CourseManager;
use App\Manager\OrderManager;
use App\Manager\TraineeManager;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class CourseController extends AbstractController
{
    /**
     * @Route(path="/cssr/courses", name="middle_courses", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_LIST'))")
     *
     * @param Request       $request
     * @param CourseManager $courseManager
     *
     * @return Response
     */
    public function list(
        Request $request,
        CourseManager $courseManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(CourseSearchType::class);

        $form->handleRequest($request);
        $filters = $form->getData();

        if ($user->getCenter() instanceof Center) {
            $filters['code'] = $user->getCenter()->getCode();
        }

        return $this->render('middle/course/list.html.twig', [
            'courses' => $courseManager->collect($filters),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/course", name="middle_course_create", methods={"GET","POST"})
     * @Route("/cssr/course/{id}/edit", name="middle_course_edit", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_EDIT'), course)")
     *
     * @param Request       $request
     * @param Course|null   $course
     * @param CourseManager $courseManager
     *
     * @return Response
     */
    public function edit(
        Request $request,
        ?Course $course,
        CourseManager $courseManager
    ): Response {
        if (!$course instanceof Course) {
            $course = new Course();
        }

        $originalPrice = $course->getAmount();

        $form = $this->createForm(CourseType::class, $course, [
            'user' => $this->getUser(),
            'course' => $course,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($course->getId()) {
                $courseManager->update($course, $originalPrice);
            } else {
                $courseManager->create($course);
            }

            return $this->redirectToRoute('middle_courses');
        }

        return $this->render('middle/course/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/cssr/course/{id}", name="middle_course", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_VIEW'), course)")
     *
     * @param Course        $course
     * @param Request       $request
     * @param CourseManager $courseManager
     *
     * @return Response
     */
    public function view(Course $course, Request $request, CourseManager $courseManager): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'comment' => true,
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $courseManager->updateComment($course);
        }

        return $this->render('middle/course/index.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/course/{id}", name="middle_course_delete", methods={"DELETE"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_DELETE'), course)")
     *
     * @param Course              $course
     * @param CourseManager       $courseManager
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function delete(
        Course $course,
        CourseManager $courseManager,
        TranslatorInterface $translator
    ): Response {
        $courseManager->delete($course);

        return new JsonResponse($translator->trans('message.flash.courseDelete'));
    }

    /**
     * @Route(path="/cssr/course/{id}/trainee", name="middle_course_register", methods={"GET", "POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_REGISTER'), course)")
     *
     * @param Request        $request
     * @param Course         $course
     * @param TraineeManager $traineeManager
     * @param OrderManager   $orderManager
     *
     * @return Response
     */
    public function register(
        Request $request,
        Course $course,
        TraineeManager $traineeManager,
        OrderManager $orderManager
    ): Response {
        $trainee = new Trainee();

        $form = $this->createForm(TraineeType::class, $trainee, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $traineeManager->create($trainee);
            $orderManager->create($trainee, $course, Order::STATUS_CONFIRMED, null);

            return $this->redirectToRoute('middle_course', ['id' => $course->getId()]);
        }

        return $this->render('middle/trainee/edit.html.twig', [
            'trainee' => $trainee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/order/{id}/emargement", name="middle_course_emargement", methods={"GET"})
     *
     * @param Course $course
     * @param Pdf    $pdf
     *
     * @return PdfResponse
     */
    public function emargement(Course $course, Pdf $pdf): PdfResponse
    {
        $html = $this->renderView('pdf/emargement.html.twig', [
            'course' => $course,
        ]);

        $options = [
        'header-html' => $this->renderView('pdf/_header.html.twig'),
        ];

        return new PdfResponse(
            $pdf->getOutputFromHtml($html, $options),
            'emargement.pdf'
        );
    }

    /**
     * @Route("/cssr/order/{id}/attestation", name="middle_order_attestation", methods={"GET"})
     *
     * @param Course $course
     * @param Pdf    $pdf
     *
     * @return PdfResponse
     */
    public function attestation(Course $course, Pdf $pdf): PdfResponse
    {
        $html = $this->renderView('pdf/attestation.html.twig', [
            'course' => $course,
        ]);

        $options = [
            'footer-html' => $this->renderView('pdf/_footer.html.twig', [
                'center' => $course->getCenter(),
                'pager' => false,
            ]),
        ];

        return new PdfResponse(
            $pdf->getOutputFromHtml($html, $options),
            'attestation.pdf'
        );
    }
}
