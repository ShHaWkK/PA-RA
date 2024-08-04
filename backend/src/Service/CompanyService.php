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
        
        // Vérifiez si 'contact_info' est défini avant de l'utiliser
        if (isset($data['contact_info'])) {
            $company->setContactInfo($data['contact_info']);
        } else {
            // Définir une valeur par défaut ou gérer le cas où 'contact_info' n'est pas fourni
            $company->setContactInfo('');
        }

        // Vérifiez si 'renewal_date' est défini avant de l'utiliser
        if (isset($data['renewal_date'])) {
            $company->setRenewalDate(new \DateTime($data['renewal_date']));
        } else {
            // Définir une valeur par défaut ou gérer le cas où 'renewal_date' n'est pas fourni
            $company->setRenewalDate(new \DateTime());
        }

        $company->setRenewalStatus('pending');

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        error_log("Company added successfully with ID: " . $company->getId());

        return $company;
    }
}

?>
