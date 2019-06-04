<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Submission;
use App\Form\SearchType;
use App\Form\SubmissionType;
use App\Manager\FaqManager;
use App\Manager\OrderManager;
use App\Manager\PageManager;
use App\Manager\SubmissionManager;
use App\Manager\TestimonialManager;
use App\Services\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="/", name="homepage", methods={"GET"})
     *
     * @param TestimonialManager $testimonialManager
     * @param Session            $session
     * @param OrderManager       $orderManager
     *
     * @return Response
     */
    public function index(TestimonialManager $testimonialManager, OrderManager $orderManager, Session $session): Response
    {
        $form = $this->createForm(SearchType::class);

        $orderManager->clear($session);

        return $this->render('front/default/index.html.twig', [
            'testimonials' => $testimonialManager->collect(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/contact", name="contact")
     *
     * @param Request             $request
     * @param SubmissionManager   $submissionManager
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function contact(
        Request $request,
        SubmissionManager $submissionManager,
        TranslatorInterface $translator
    ): Response {
        $submission = new Submission();

        $form = $this->createForm(SubmissionType::class, $submission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submissionManager->create($submission);
            // @TODO: MessageService implementation
            $this->addFlash(
                'notice',
                $translator->trans('message.flash.submissionOk')
            );

            return $this->redirectToRoute('contact');
        }

        return $this->render('front/default/submission.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/faq", name="faq")
     *
     * @param FaqManager $faqManager
     *
     * @return Response
     */
    public function faq(FaqManager $faqManager): Response
    {
        return $this->render('front/default/faq.html.twig', [
            'faqs' => $faqManager->collect(),
        ]);
    }

    /**
     * @Route(path="/page/{slug}", name="page", methods={"GET"})
     *
     * @param Page $page
     *
     * @return Response
     */
    public function page(Page $page): Response
    {
        return $this->render('front/default/page.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route(path="/sitemap.{_format}", defaults={"_format"="xml"}, name="sitemap", methods={"GET"})
     *
     * @param PageRepository $pageRepository
     *
     * @return Response
     */
    public function sitemap(
        Request $request,
        PageManager $pageManager
    ): Response {
        return $this->render('front/default/sitemap.xml.twig', [
            'pages' => $pageManager->collect(),
        ]);
    }
}
