<?php
// Path: backend/src/Entity/CompanyModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "companies")]
class CompanyModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "string", length: 255)]
    private $address;

    #[ORM\Column(type: "string", length: 255)]
    private $contact_info;

    #[ORM\Column(type: "string", length: 14)]
    private $siret;

    #[ORM\Column(type: "date")]
    private $renewal_date;

    #[ORM\Column(type: "string", length: 50, options: ["default" => "pending"])]
    private $renewal_status;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $has_stock;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $last_notified;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getContactInfo(): ?string
    {
        return $this->contact_info;
    }

    public function setContactInfo(string $contactInfo): self
    {
        $this->contact_info = $contactInfo;
        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;
        return $this;
    }

    public function getRenewalDate(): ?\DateTimeInterface
    {
        return $this->renewal_date;
    }

    public function setRenewalDate(\DateTimeInterface $renewalDate): self
    {
        $this->renewal_date = $renewalDate;
        return $this;
    }

    public function getRenewalStatus(): ?string
    {
        return $this->renewal_status;
    }

    public function setRenewalStatus(string $renewalStatus): self
    {
        $this->renewal_status = $renewalStatus;
        return $this;
    }

    public function getHasStock(): bool
    {
        return $this->has_stock;
    }

    public function setHasStock(bool $has_stock): self
    {
        $this->has_stock = $has_stock;
        return $this;
    }

    public function getLastNotified(): ?\DateTimeInterface
    {
        return $this->last_notified;
    }

    public function setLastNotified(?\DateTimeInterface $lastNotified): self
    {
        $this->last_notified = $lastNotified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    // Implémentation de la méthode jsonSerialize
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'contact_info' => $this->getContactInfo(),
            'siret' => $this->getSiret(),
            'renewal_date' => $this->getRenewalDate()->format('Y-m-d'),
            'renewal_status' => $this->getRenewalStatus(),
            'has_stock' => $this->getHasStock(),
            'last_notified' => $this->getLastNotified()?->format('Y-m-d H:i:s'),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
?>
