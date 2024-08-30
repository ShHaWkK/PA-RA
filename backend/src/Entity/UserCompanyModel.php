<?php
// Path: backend/src/Entity/UserCompanyModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_companies")]
class UserCompanyModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: "UserModel", inversedBy: "user_companies")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $user;

    #[ORM\ManyToOne(targetEntity: "CompanyModel", inversedBy: "user_companies")]
    #[ORM\JoinColumn(name: "company_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $company;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('employee', 'manager', 'merchant')")]
    private $role;

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(UserModel $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCompany(): ?CompanyModel
    {
        return $this->company;
    }

    public function setCompany(CompanyModel $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

        public function jsonSerialize(): array
    {
        return [
            'user_id' => $this->getUser()->getId(),
            'user_name' => $this->getUser()->getFirstName() . ' ' . $this->getUser()->getLastName(),
            'user_mail' => $this->getUser()->getEmail(),
            'user_phone' => $this->getUser()->getPhoneNumber(),
            'role' => $this->getRole(),
            'company_id' => $this->getCompany()->getId(),
            'company_name' => $this->getCompany()->getName()
        ];
    }

}
?>
