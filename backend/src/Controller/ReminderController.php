<?php
// Path: backend/src/Controller/ReminderController.php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Entity\CompanyModel;

class ReminderController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        switch ($method) {
            case 'POST':
                return $this->sendReminders();
            default:
                http_response_code(405);
                return ['error' => 'Method Not Allowed'];
        }
    }

    public function sendReminders()
    {
        $today = new \DateTime();
        $nextWeek = (clone $today)->modify('+1 week');

        $query = $this->entityManager->createQuery('SELECT c FROM Entity\CompanyModel c WHERE c.renewal_date <= :next_week AND c.renewal_status = :pending')
            ->setParameter('next_week', $nextWeek)
            ->setParameter('pending', 'pending');

        $companies = $query->getResult();

        foreach ($companies as $company) {
            $to = $company->getContactInfo();
            $subject = "Rappel de renouvellement d'adhésion";
            $message = "Bonjour " . $company->getName() . ",\n\nVotre adhésion est sur le point d'expirer. Veuillez renouveler votre adhésion avant le " . $company->getRenewalDate()->format('Y-m-d') . ".\n\nCordialement,\nNo More Waste";

            mail($to, $subject, $message);

            $company->setRenewalStatus('notified');
            $this->entityManager->persist($company);
        }

        $this->entityManager->flush();

        return ['message' => 'Reminders sent successfully'];
    }
}

?>