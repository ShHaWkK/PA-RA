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

    public function addCompany($data, UserModel $user)
    {
        $company = new CompanyModel();
        $company->setName($data['company_name']);
        $company->setSiret($data['siret']);
        $company->setAddress($data['address']);
        $company->setRenewalDate(new \DateTime($data['renewal_date']));
        $company->setUser($user);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $company;
    }
}
?>
