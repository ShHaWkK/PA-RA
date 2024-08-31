<?php
// Path: backend/src/Service/CompanyService.php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\CompanyModel;
use Entity\UserModel;

class CompanyService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addCompany($data)
    {
        // Validation des données
        if (empty($data['company_name']) || empty($data['siret']) || empty($data['address']) || empty($data['renewal_date'])) {
            throw new \InvalidArgumentException('Missing required company data.');
        }

        try {
            // Début de la transaction
            $this->entityManager->beginTransaction();

            error_log("Adding company with name: " . $data['company_name']);

            $company = new CompanyModel();
            $company->setName($data['company_name']);
            $company->setSiret($data['siret']);
            $company->setAddress($data['address']);
            $company->setContactInfo($data['contact_info'] ?? ''); // Utiliser une chaîne vide par défaut si non fourni
            $company->setRenewalDate(new \DateTime($data['renewal_date']));
            $company->setRenewalStatus($data['renewal_status'] ?? 'pending');
            $company->setHasStock($data['has_stock'] ?? false);
            $company->setLastNotified(isset($data['last_notified']) ? new \DateTime($data['last_notified']) : null);

            $this->entityManager->persist($company);
            $this->entityManager->flush();

            // Validation de la transaction
            $this->entityManager->commit();

            error_log("Company added successfully with ID: " . $company->getId());

            return $company;
        } catch (\Exception $e) {
            // Annulation de la transaction en cas d'erreur
            $this->entityManager->rollback();
            error_log("Error adding company: " . $e->getMessage());
            throw $e;
        }
    }
}
?>