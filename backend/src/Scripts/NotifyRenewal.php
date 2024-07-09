<?php
// Path: backend/src/Scripts/NotifyRenewal.php
require_once __DIR__ . '/../../bootstrap.php';

use Service\EmailService;
use Entity\CompanyModel;

$entityManager = GetEntityManager();
$emailService = new EmailService('SENDGRID_API_KEY');

$today = new \DateTime();
$notificationDate = (clone $today)->modify('+15 days');

$companies = $entityManager->getRepository(CompanyModel::class)->createQueryBuilder('c')
    ->where('c.renewal_date = :renewalDate')
    ->andWhere('c.last_notified IS NULL OR c.last_notified < :lastNotifiedDate')
    ->setParameter('renewalDate', $notificationDate->format('Y-m-d'))
    ->setParameter('lastNotifiedDate', $today->format('Y-m-d'))
    ->getQuery()
    ->getResult();

foreach ($companies as $company) {
    $emailService->sendEmail(
        $company->getContactInfo(),
        'Renewal Reminder',
        "Dear {$company->getName()},\n\nYour subscription is due for renewal on {$company->getRenewalDate()->format('Y-m-d')}. Please renew it promptly.\n\nThank you."
    );

    $company->setLastNotified(new \DateTime());
    $entityManager->flush();
}
?>