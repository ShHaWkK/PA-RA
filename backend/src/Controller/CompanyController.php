<?php
// Path: backend/src/Controller/CompanyController.php
namespace Controller;

use Entity\CompanyModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;

class CompanyController
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
        try {
            switch ($method) {
                case 'POST':
                    return $this->createCompany($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getCompany((int) $uriParts[1]);
                    } else {
                        return $this->getAllCompanies();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateCompany((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Company ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteCompany((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Company ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createCompany($data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['name']) || !isset($data['address']) || !isset($data['contact_info']) || !isset($data['siret']) || !isset($data['renewal_date']) || !isset($data['renewal_status'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new company'];
            }

            $company = new CompanyModel();
            $company->setName($data['name']);
            $company->setAddress($data['address']);
            $company->setContactInfo($data['contact_info']);
            $company->setSiret($data['siret']);
            $company->setRenewalDate(new \DateTime($data['renewal_date']));
            $company->setRenewalStatus($data['renewal_status']);
            $company->setCreatedAt(new \DateTime("now"));
            $company->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($company);
            $this->entityManager->flush();

            return ['id' => $company->getId(), 'message' => 'Company created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createCompany: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCompany($id)
    {
        try {
            $company = $this->entityManager->find(CompanyModel::class, $id);
            if (!$company) {
                http_response_code(404);
                return ['error' => 'Company not found'];
            }
            return json_decode($this->serializer->serialize($company, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getCompany: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateCompany($id, $data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['name']) && !isset($data['address']) && !isset($data['contact_info']) && !isset($data['siret']) && !isset($data['renewal_date']) && !isset($data['renewal_status'])) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $company = $this->entityManager->find(CompanyModel::class, $id);
            if (!$company) {
                http_response_code(404);
                return ['error' => 'Company not found'];
            }

            if (isset($data['name'])) {
                $company->setName($data['name']);
            }
            if (isset($data['address'])) {
                $company->setAddress($data['address']);
            }
            if (isset($data['contact_info'])) {
                $company->setContactInfo($data['contact_info']);
            }
            if (isset($data['siret'])) {
                $company->setSiret($data['siret']);
            }
            if (isset($data['renewal_date'])) {
                $company->setRenewalDate(new \DateTime($data['renewal_date']));
            }
            if (isset($data['renewal_status'])) {
                $company->setRenewalStatus($data['renewal_status']);
            }
            $company->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $company->getId(), 'message' => 'Company updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateCompany: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCompany($id)
    {
        try {
            $company = $this->entityManager->find(CompanyModel::class, $id);
            if (!$company) {
                http_response_code(404);
                return ['error' => 'Company not found'];
            }

            $this->entityManager->remove($company);
            $this->entityManager->flush();

            return ['message' => 'Company deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteCompany: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllCompanies()
    {
        try {
            $companyRepository = $this->entityManager->getRepository(CompanyModel::class);
            $companies = $companyRepository->findAll();
            return json_decode($this->serializer->serialize($companies, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllCompanies: " . $e->getMessage());
            throw $e;
        }
    }
}
