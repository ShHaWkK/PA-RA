<?php
namespace Service;

use Doctrine\ORM\EntityManager;
use Entity\SkillModel;
use Entity\UserSkillModel;
use Entity\UserModel;

class SkillService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addSkills(UserModel $user, array $skills)
    {
        foreach ($skills as $skillId) {
            $skill = $this->entityManager->find(SkillModel::class, $skillId);
            $userSkill = new UserSkillModel();
            $userSkill->setUserId($user->getId());
            $userSkill->setSkillId($skill->getId());
            $this->entityManager->persist($userSkill);
        }

        $this->entityManager->flush();
    }
}
?>