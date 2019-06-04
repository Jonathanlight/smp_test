<?php

namespace App\Services;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\Export;
use App\Entity\Option;
use App\Entity\Order;
use App\Entity\Trainee;
use App\Manager\OrderManager;
use App\Manager\PaymentManager;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\TranslatorInterface;

class ReportingService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var PaymentManager
     */
    private $paymentManager;

    /**
     * @var OrderManager
     */
    private $orderManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     * @param OrderRepository        $orderRepository
     * @param PaymentManager         $paymentManager
     * @param OrderManager           $orderManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        OrderRepository $orderRepository,
        PaymentManager $paymentManager,
        OrderManager $orderManager
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->orderRepository = $orderRepository;
        $this->paymentManager = $paymentManager;
        $this->orderManager = $orderManager;
    }

    /**
     * @param Form $form
     *
     * @return StreamedResponse
     */
    public function exportOrder(Form $form)
    {
        $data = $form->getData();

        $response = new StreamedResponse();

        $repository = $this->orderRepository;

        $response->setCallback(function () use ($repository, $data) {
            $handle = fopen('php://output', 'w+');

            $tabHeader = [
                'Ref stagiaire',
                "Date d'achat",
                'Nom',
                'Prénom',
                'Email',
                'Adresse',
                'CP',
                'Ville',
                'Num tel',
                'Date de stage',
                'Lieu de stage',
                'Num CSSR',
                'Statut',
                'Cas',
                'Montant TTC du stage',
                'Montant TTC facture SMP',
                'Montant TTC Commission SMP',
                'Date comptabilisée', // Date export "ExportApplicationFee" (facture stagiaire)
                'Date de remboursement',
            ];

            foreach ($tabHeader as $key => $value) {
                $tabHeader[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
            }

            fputcsv($handle, $tabHeader, ';');

            foreach ($repository->getOrderForExport($data) as $stagiaire) {
                if (!$stagiaire->getTrainee() instanceof Trainee) {
                    continue;
                }

                if ($stagiaire->getTrainee()) {
                    if ($stagiaire->getTrainee()->getReference()) {
                        switch ($stagiaire->getTrainee()->getCourseType()) {
                            case Trainee::TYPE_VOLUNTARY:
                                $typeCourse = 1;
                                break;
                            case Trainee::TYPE_REQUIRED:
                                $typeCourse = 2;
                                break;
                            case Trainee::TYPE_JUDICIAL:
                                $typeCourse = 3;
                                break;
                            case Trainee::TYPE_SENTENCE:
                                $typeCourse = 4;
                                break;
                            default:
                                $typeCourse = '';
                        }

                        switch ($stagiaire->getStatus()) {
                            case Order::STATUS_CONFIRMED:
                                $status = $this->translator->trans('info.order.state.confirmed');
                                break;
                            case Order::STATUS_CANCELLED:
                                $status = $this->translator->trans('info.order.state.cancelled');
                                break;
                            case Order::STATUS_WAITING:
                                $status = $this->translator->trans('info.order.state.waiting');
                                break;
                            case Order::STATUS_PENDING:
                                $status = $this->translator->trans('info.order.state.pending');
                                break;
                            case Order::STATUS_REFUNDED:
                                $status = $this->translator->trans('info.order.state.refunded');
                                break;
                            case Order::STATUS_REGISTERED:
                                $status = $this->translator->trans('info.order.state.registered');
                                break;
                            default:
                                $status = '';
                        }

                        $tabContent = [
                            $stagiaire->getTrainee()->getReference(),
                            (null == $stagiaire->getPaidAt()) ? '' : date_format($stagiaire->getPaidAt(), 'd/m/Y'),
                            $stagiaire->getTrainee()->getLastName(),
                            $stagiaire->getTrainee()->getFirstName(),
                            $stagiaire->getTrainee()->getEmail(),
                            $stagiaire->getTrainee()->getAddress(),
                            $stagiaire->getTrainee()->getPostalCode(),
                            $stagiaire->getTrainee()->getCity(),
                            /*
                             * @todo Revoir comment supprimer le point
                             */
                            '.'.$stagiaire->getTrainee()->getPhone(),
                            date_format($stagiaire->getCourse()->getStartOn(), 'd/m/Y'),
                            $stagiaire->getCourse()->getPlace()->getCity(),
                            $stagiaire->getCourse()->getCenter()->getCode(),
                            $status,
                            $typeCourse,
                            $stagiaire->getAmount() - $this->getOptionsOrder($stagiaire),
                            $stagiaire->getAmount(),
                            $stagiaire->getCommission() + ($stagiaire->getCommission() * 0.2),
                            $stagiaire->hasExportType(Export::EXPORT_FEE) ? $stagiaire->getDateExportType(Export::EXPORT_FEE) : '',
                            $stagiaire->hasExportType(Export::EXPORT_REFUND) ? $stagiaire->getDateExportType(Export::EXPORT_REFUND) : '',
                        ];

                        foreach ($tabContent as $key => $value) {
                            $tabContent[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
                        }

                        fputcsv($handle, $tabContent, ';');
                    }
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-stagiaires.csv"');

        return $response;
    }

    /**
     * @param Center $center
     * @param Form   $form
     *
     * @return StreamedResponse
     */
    public function exportCssr(Center $center, Form $form)
    {
        $data = $form->getData();

        $response = new StreamedResponse();

        $repository = $this->orderRepository;

        $response->setCallback(function () use ($repository, $center, $data) {
            $handle = fopen('php://output', 'w+');

            $tabHeader = [
                'Ref stagiaire',
                "Date d'achat",
                'Nom',
                'Prénom',
                'Date de stage',
                'Lieu de stage',
                'Num CSSR',
                'Nom animateur 1',
                'Nom animateur 2',
                'Statut commande',
                'Cas',
                'Montant TTC du stage',
                'Montant TTC Commission SMP',
                'Date comptabilisée', // Date export bancaire
            ];

            foreach ($tabHeader as $key => $value) {
                $tabHeader[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
            }

            fputcsv($handle, $tabHeader, ';');

            foreach ($repository->getOrderForExportCssr($center, $data) as $stagiaire) {
                if (!$stagiaire->getTrainee() instanceof Trainee) {
                    continue;
                }

                if ($stagiaire->getTrainee()) {
                    if ($stagiaire->getTrainee()->getReference()) {
                        switch ($stagiaire->getTrainee()->getCourseType()) {
                            case Trainee::TYPE_VOLUNTARY:
                                $typeCourse = 1;
                                break;
                            case Trainee::TYPE_REQUIRED:
                                $typeCourse = 2;
                                break;
                            case Trainee::TYPE_JUDICIAL:
                                $typeCourse = 3;
                                break;
                            case Trainee::TYPE_SENTENCE:
                                $typeCourse = 4;
                                break;
                            default:
                                $typeCourse = '';
                        }

                        switch ($stagiaire->getStatus()) {
                            case Order::STATUS_CONFIRMED:
                                $status = $this->translator->trans('info.order.state.confirmed');
                                break;
                            case Order::STATUS_CANCELLED:
                                $status = $this->translator->trans('info.order.state.cancelled');
                                break;
                            case Order::STATUS_WAITING:
                                $status = $this->translator->trans('info.order.state.waiting');
                                break;
                            case Order::STATUS_PENDING:
                                $status = $this->translator->trans('info.order.state.pending');
                                break;
                            case Order::STATUS_REFUNDED:
                                $status = $this->translator->trans('info.order.state.refunded');
                                break;
                            case Order::STATUS_REGISTERED:
                                $status = $this->translator->trans('info.order.state.registered');
                                break;
                            default:
                                $status = '';
                        }

                        $tabContent = [
                            $stagiaire->getTrainee()->getReference(),
                            (null == $stagiaire->getPaidAt()) ? '' : date_format($stagiaire->getPaidAt(), 'd/m/Y'),
                            $stagiaire->getTrainee()->getLastName(),
                            $stagiaire->getTrainee()->getFirstName(),
                            date_format($stagiaire->getCourse()->getStartOn(), 'd/m/Y'),
                            $stagiaire->getCourse()->getPlace()->getCity(),
                            $stagiaire->getCourse()->getCenter()->getCode(),
                            $stagiaire->getCourse()->getFormer(),
                            $stagiaire->getCourse()->getPsychologist(),
                            $status,
                            $typeCourse,
                            $stagiaire->getAmount() - $this->getOptionsOrder($stagiaire),
                            $stagiaire->getCommission() + ($stagiaire->getCommission() * 0.2),
                            $stagiaire->hasExportType(Export::EXPORT_BANK) ? $stagiaire->getDateExportType(Export::EXPORT_BANK) : '',
                        ];

                        foreach ($tabContent as $key => $value) {
                            $tabContent[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
                        }

                        fputcsv($handle, $tabContent, ';');
                    }
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-cssr.csv"');

        return $response;
    }

    /**
     * @return StreamedResponse
     */
    public function exportApplicationFee()
    {
        $response = new StreamedResponse();

        $response->setCallback(function () {
            $handle = fopen('php://output', 'w+');

            $tabHeader = [
                'Ref stagiaire',
                "Date d'achat",
                'Nom',
                'Prénom',
                'Email',
                'Date de stage',
                'Lieu de stage',
                'Num CSSR',
                'Statut',
                'Montant TTC facture SMP',
                'Num facture SMP',
                'Montant des frais de dossier TTC',
                "Montant frais d'envoi par courrier ttc",
            ];

            foreach ($tabHeader as $key => $value) {
                $tabHeader[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
            }

            fputcsv($handle, $tabHeader, ';');

            foreach ($this->orderRepository->getOrderByStatus(Order::STATUS_CONFIRMED) as $order) {
                if ($order->getTrainee()) {
                    if ($order->getTrainee()->getReference()) {
                        if ($order->hasExportType(Export::EXPORT_BANK) ||
                          $order->hasExportType(Export::EXPORT_REFUND)) {
                            continue;
                        }

                        $optionOrder = 0;
                        $amountShipping = Option::OPTION_AMOUNT_APPLICATION_FEE;

                        foreach ($order->getOptions() as $option) {
                            if (Option::CODE_SEND_LETTER == $option->getCode()) {
                                $optionOrder = Option::OPTION_AMOUNT_SEND_LETTER;
                            }
                        }

                        if ($order->getCoupon()) {
                            $amountShipping -= $order->getCoupon()->getAmount();
                        }

                        $tabContent = [
                            $order->getTrainee()->getReference(),
                            (null == $order->getPaidAt()) ? '' : date_format($order->getPaidAt(), 'd/m/Y'),
                            $order->getTrainee()->getLastName(),
                            $order->getTrainee()->getFirstName(),
                            $order->getTrainee()->getEmail(),
                            date_format($order->getCourse()->getStartOn(), 'd/m/Y'),
                            $order->getCourse()->getPlace()->getCity(),
                            $order->getCourse()->getCenter()->getCode(),
                            $this->translator->trans('info.order.state.confirmed'),
                            $order->getAmount(),
                            $order->getReference(),
                            $amountShipping,
                            $optionOrder,
                        ];

                        foreach ($tabContent as $key => $value) {
                            $tabContent[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
                        }

                        fputcsv($handle, $tabContent, ';');

                        $export = new Export();
                        $export->setType(Export::EXPORT_FEE);
                        $export->setExportedAt(new \DateTime());

                        $order->addExport($export);

                        $this->em->persist($order);

                        $this->em->flush();
                    }
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-factures-stagiaire.csv"');

        return $response;
    }

    /**
     * @return StreamedResponse
     */
    public function exportOrderRefund()
    {
        $response = new StreamedResponse();

        $response->setCallback(function () {
            $handle = fopen('php://output', 'w+');

            $tabHeader = [
                'Ref stagiaire',
                "Date d'achat",
                'Nom',
                'Prénom',
                'Email',
                'Date de stage',
                'Lieu de stage',
                'Num CSSR',
                'Motif du remboursement',
                'Montant remboursé',
                'Statut',
                'Montant TTC facture SMP',
                'Num facture SMP',
                'Montant TTC du stage CSSR',
            ];

            foreach ($tabHeader as $key => $value) {
                $tabHeader[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
            }

            fputcsv($handle, $tabHeader, ';');

            foreach ($this->orderRepository->getOrderByRefund() as $order) {
                if ($order->getTrainee()) {
                    if ($order->getTrainee()->getReference()) {
                        if (!$order->hasExportType(Export::EXPORT_REFUND)) {
                            $tabContent = [
                                $order->getTrainee()->getReference(),
                                (null == $order->getPaidAt()) ? '' : date_format($order->getPaidAt(), 'd/m/Y'),
                                $order->getTrainee()->getLastName(),
                                $order->getTrainee()->getFirstName(),
                                $order->getTrainee()->getEmail(),
                                date_format($order->getCourse()->getStartOn(), 'd/m/Y'),
                                $order->getCourse()->getPlace()->getCity(),
                                $order->getCourse()->getCenter()->getCode(),
                                $order->getReason(),
                                $order->getRefundedAmount(),
                                $this->translator->trans('info.order.state.refunded'),
                                $order->getAmount(),
                                $order->getReference(),
                                $order->getAmount() - $this->getOptionsOrder($order),
                            ];

                            foreach ($tabContent as $key => $value) {
                                $tabContent[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
                            }

                            fputcsv($handle, $tabContent, ';');

                            $export = new Export();
                            $export->setExportedAt(new \DateTime());
                            $export->setType(Export::EXPORT_REFUND);

                            $order->addExport($export);

                            $this->em->persist($order);
                            $this->em->persist($export);

                            $this->em->flush();
                        }
                    }
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-remboursement.csv"');

        return $response;
    }

    /**
     * @param array $orders
     *
     * @return \DOMDocument
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function exportBank(array $orders)
    {
        $xml = new \DOMDocument('1.0', 'utf-8');
        $document = $xml->createElement('Document');
        $document->setAttribute('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.02');

        $documentContent = $document->appendChild($xml->createElement('pain.001.001.02'));
        $grpHdr = $documentContent->appendChild($xml->createElement('GrpHdr'));

        $xml->appendChild($document);

        $grpHdr->appendChild($xml->createElement('MsgId', 'PAI MED'));
        $grpHdr->appendChild($xml->createElement('CreDtTm', date('Y-m-d').'T'.date('H:i:s')));

        $pmtInf = $documentContent->appendChild($xml->createElement('PmtInf'));
        $pmtInf->appendChild($xml->createElement('PmtInfId', 'PAI MED'));
        $pmtInf->appendChild($xml->createElement('PmtMtd', 'TRF'));
        $pmtTpInf = $pmtInf->appendChild($xml->createElement('PmtTpInf'));
        $pmtInf->appendChild($xml->createElement('ReqdExctnDt', date('Y-m-d')));
        $dbtr = $pmtInf->appendChild($xml->createElement('Dbtr'));
        $dbtr->appendChild($xml->createElement('Nm', 'SARL SMP'));
        $dbtrAcct = $pmtInf->appendChild($xml->createElement('DbtrAcct'));
        $id = $dbtrAcct->appendChild($xml->createElement('Id'));
        $DbtrAgt = $pmtInf->appendChild($xml->createElement('DbtrAgt'));
        $finInstnId = $DbtrAgt->appendChild($xml->createElement('FinInstnId'));

        $svcLvl = $pmtTpInf->appendChild($xml->createElement('SvcLvl'));

        $svcLvl->appendChild($xml->createElement('Cd', 'SEPA'));

        $id->appendChild($xml->createElement('IBAN', 'FR7630003034500002061387640'));
        $finInstnId->appendChild($xml->createElement('BIC', 'SOGEFRPP'));

        $total = 0;
        $centers = [];
        $nbOrders = 0;

        foreach ($orders as $order) {
            if ($order->getTrainee()) {
                if ($order->getTrainee()->getReference()) {
                    if (!$order->hasExportType(Export::EXPORT_BANK) &&
                        !$order->hasExportType(Export::EXPORT_REFUND)) {
                        if (!isset($centers[$order->getCourse()->getCenter()->getId()])) {
                            $centers[$order->getCourse()->getCenter()->getId()] = [
                                'amount' => 0,
                                'courses' => [],
                            ];
                        }

                        $amount = $order->getAmount() - $this->getOptionsOrder($order);

                        if ($order->getCommission()) {
                            $commission = $order->getCommission() + ($order->getCommission() * 0.2);
                            $amount -= $commission;
                        }

                        $centers[$order->getCourse()->getCenter()->getId()]['amount'] += $amount;
                        $centers[$order->getCourse()->getCenter()->getId()]['courses'][] = $order->getCourse()->getId();
                    }
                }
            }
        }

        foreach ($centers as $key => $data) {
            $center = $this->em->getRepository(Center::class)->find($key);
            $payment = $this->paymentManager->create($center, $data['amount']);

            foreach ($data['courses'] as $course) {
                $course = $this->em->getRepository(Course::class)->find($course);
                $course->setPayment($payment);
            }

            $center = $this->em->getRepository(Center::class)->find($key);

            $cdtTrfTxInf = $pmtInf->appendChild($xml->createElement('CdtTrfTxInf'));
            $pmtId = $cdtTrfTxInf->appendChild($xml->createElement('PmtId'));
            $amt = $cdtTrfTxInf->appendChild($xml->createElement('Amt'));
            $cdtrAgt = $cdtTrfTxInf->appendChild($xml->createElement('CdtrAgt'));
            $finInstnId = $cdtrAgt->appendChild($xml->createElement('FinInstnId'));
            $cdtr = $cdtTrfTxInf->appendChild($xml->createElement('Cdtr'));
            $cdtrAcct = $cdtTrfTxInf->appendChild($xml->createElement('CdtrAcct'));
            $id = $cdtrAcct->appendChild($xml->createElement('Id'));
            $rmtInf = $cdtTrfTxInf->appendChild($xml->createElement('RmtInf'));

            $pmtId->appendChild($xml->createElement('EndToEndId', 'PAI MED'));
            $amt->appendChild($instdAmt = $xml->createElement('InstdAmt', $data['amount']));
            $instdAmt->setAttribute('Ccy', 'EUR');
            $finInstnId->appendChild($xml->createElement('BIC', $center->getBic()));
            $cdtr->appendChild($xml->createElement('Nm', $center->getName().' '.$center->getLastName().' '.$center->getFirstName()));
            $id->appendChild($xml->createElement('IBAN', str_replace(' ', '', $center->getIban())));
            $rmtInf->appendChild($xml->createElement('Ustrd', $payment->getReference()));

            ++$nbOrders;
            $total += $data['amount'];
        }

        $this->em->flush();

        $grpHdr->appendChild($xml->createElement('NbOfTxs', $nbOrders));
        $grpHdr->appendChild($xml->createElement('CtrlSum', $total));
        $grpHdr->appendChild($xml->createElement('Grpg', 'MIXD'));
        $initgPty = $grpHdr->appendChild($xml->createElement('InitgPty'));
        $initgPty->appendChild($xml->createElement('Nm', 'SARL SMP'));

        return $xml;
    }

    /**
     * @param array $orders
     *
     * @return StreamedResponse
     */
    public function exportBankCsv(array $orders)
    {
        $handle = fopen('État des charges et règlement CSSR.csv', 'w+');

        $tabHeader = [
            'Num CSSR',
            'Nom CSSR',
            'Ref stagiaire',
            "Date d'achat",
            'Nom',
            'Prénom',
            'Date de stage',
            'Lieu de stage',
            'Statut',
            'Montant TTC du stage en FO',
            'Num facture',
            'Montant TTC Commission SMP',
            'Montant Réglé au CSSR',
            'Date de règlement',
        ];

        foreach ($tabHeader as $key => $value) {
            $tabHeader[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
        }

        fputcsv($handle, $tabHeader, ';');

        foreach ($orders as $order) {
            if ($order->getTrainee()) {
                if ($order->getTrainee()->getReference()) {
                    if (!$order->hasExportType(Export::EXPORT_BANK) &&
                        !$order->hasExportType(Export::EXPORT_REFUND)) {
                        $amount = $order->getAmount();

                        if ($order->getCommission()) {
                            $commission = $order->getCommission() + ($order->getCommission() * 0.2);
                            $amount -= $commission;
                        }

                        $amount -= $this->getOptionsOrder($order);

                        $tabContent = [
                            $order->getCourse()->getCenter()->getCode(),
                            $order->getCourse()->getCenter()->getName().' '.$order->getCourse()->getCenter()->getLastName().' '.$order->getCourse()->getCenter()->getFirstName(),
                            $order->getTrainee()->getReference(),
                            (null == $order->getPaidAt()) ? '' : date_format($order->getPaidAt(), 'd/m/Y'),
                            $order->getTrainee()->getLastName(),
                            $order->getTrainee()->getFirstName(),
                            date_format($order->getCourse()->getStartOn(), 'd/m/Y'),
                            $order->getCourse()->getPlace()->getCity(),
                            $this->translator->trans('info.order.state.confirmed'),
                            $order->getAmount() - $this->getOptionsOrder($order),
                            $order->getCourse()->getPayment()->getReference(),
                            $order->getCommission() + ($order->getCommission() * 0.2),
                            $amount,
                            date('d-m-Y'),
                        ];

                        foreach ($tabContent as $key => $value) {
                            $tabContent[$key] = \mb_convert_encoding($value, 'windows-1252', 'utf-8');
                        }

                        fputcsv($handle, $tabContent, ';');

                        $this->em->persist($order);

                        $this->em->flush();
                    }
                }
            }
        }

        fclose($handle);

        return $handle;
    }

    /**
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExportBankZip()
    {
        $orders = $this->orderRepository->getOrderForExportBank();

        $xml = $this->exportBank($orders);

        $this->exportBankCsv($orders);

        $fileNameCsv = 'État des charges et règlement CSSR.csv';

        $archive = new \ZipArchive();
        $zipName = 'export_bancaire.zip';
        $archive->open($zipName, \ZipArchive::CREATE);
        $archive->addFile($fileNameCsv);
        $archive->addFromString('export_bancaire_xml.xml', $xml->saveXML());
        $archive->close();

        unlink($fileNameCsv);

        foreach ($orders as $order) {
            if ($order->getTrainee()) {
                if ($order->getTrainee()->getReference()) {
                    if (!$order->hasExportType(Export::EXPORT_BANK) &&
                        !$order->hasExportType(Export::EXPORT_REFUND)) {
                        $export = new Export();
                        $export->setType(Export::EXPORT_BANK);
                        $export->setExportedAt(new \DateTime());

                        $order->addExport($export);

                        $this->em->persist($order);
                    }
                }
            }
        }

        $this->em->flush();

        return $zipName;
    }

    /**
     * @param Order $order
     *
     * @return float|int|null
     */
    public function getOptionsOrder(Order $order): float
    {
        $amountOption = 0;

        if ($order->getOptions()) {
            foreach ($order->getOptions() as $option) {
                if (Option::CODE_APPLICATION_FEE === $option->getCode()) {
                    if ($order->getCoupon()) {
                        $optionFee = $option->getAmount() - $order->getCoupon()->getAmount();
                        $amountOption += $optionFee;
                    } else {
                        $amountOption += $option->getAmount();
                    }
                } else {
                    $amountOption += $option->getAmount();
                }
            }
        }

        return $amountOption;
    }
}
