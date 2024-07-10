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
        error_log("Adding company with name: " . $data['company_name']);

        $company = new CompanyModel();
        $company->setName($data['company_name']);
        $company->setSiret($data['siret']);
        $company->setAddress($data['address']);
        $company->setContactInfo($data['contact_info']);
        $company->setRenewalDate(new \DateTime($data['renewal_date']));
        $company->setRenewalStatus('pending');


        $this->entityManager->persist($company);
        $this->entityManager->flush();

        error_log("Company added successfully with ID: " . $company->getId());

        return $company;
    }

}
?>
