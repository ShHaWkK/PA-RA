<?php
// Path: backend/src/Controller/SkillController.php
namespace Controller;

use Entity\SkillModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityNotFoundException;

class SkillController
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
                    return $this->createSkill($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getSkill((int) $uriParts[1]);
                    } else {
                        return $this->getAllSkills();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateSkill((int) $uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Skill ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteSkill((int) $uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Skill ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createSkill($data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['name']) || !isset($data['description'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new skill'];
            }

            $skill = new SkillModel();
            $skill->setName($data['name']);
            $skill->setDescription($data['description']);
            $skill->setCreatedAt(new \DateTime("now"));
            $skill->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->persist($skill);
            $this->entityManager->flush();

            return ['id' => $skill->getId(), 'message' => 'Skill created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createSkill: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSkill($id)
    {
        try {
            $skill = $this->entityManager->find(SkillModel::class, $id);
            if (!$skill) {
                http_response_code(404);
                return ['error' => 'Skill not found'];
            }
            return json_decode($this->serializer->serialize($skill, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getSkill: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateSkill($id, $data)
    {
        try {
            // Validate input data (add your own validation logic)
            if (!isset($data['name']) && !isset($data['description'])) {
                http_response_code(400);
                return ['error' => 'No fields to update'];
            }

            $skill = $this->entityManager->find(SkillModel::class, $id);
            if (!$skill) {
                http_response_code(404);
                return ['error' => 'Skill not found'];
            }

            if (isset($data['name'])) {
                $skill->setName($data['name']);
            }
            if (isset($data['description'])) {
                $skill->setDescription($data['description']);
            }
            $skill->setUpdatedAt(new \DateTime("now"));

            $this->entityManager->flush();

            return ['id' => $skill->getId(), 'message' => 'Skill updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateSkill: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteSkill($id)
    {
        try {
            $skill = $this->entityManager->find(SkillModel::class, $id);
            if (!$skill) {
                http_response_code(404);
                return ['error' => 'Skill not found'];
            }

            $this->entityManager->remove($skill);
            $this->entityManager->flush();

            return ['message' => 'Skill deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteSkill: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllSkills()
    {
        try {
            $skillRepository = $this->entityManager->getRepository(SkillModel::class);
            $skills = $skillRepository->findAll();
            return json_decode($this->serializer->serialize($skills, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllSkills: " . $e->getMessage());
            throw $e;
        }
    }
}
