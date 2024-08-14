<?php
// Path: backend/src/Entity/ProductNotificationModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

#[ORM\Entity]
#[ORM\Table(name: "product_notifications")]
class ProductNotificationModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $company_id;

    #[ORM\ManyToOne(targetEntity: "CompanyModel")]
    #[ORM\JoinColumn(name: "company_id", referencedColumnName: "id")]
    private $company;

    #[ORM\Column(type: "integer")]
    private $product_id;

    #[ORM\ManyToOne(targetEntity: "ProductModel")]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    private $product;

    #[ORM\Column(type: "integer")]
    private $notified_quantity;

    #[ORM\Column(type: "string", length: 255)]
    private $address;

    #[ORM\Column(type: "datetime")]
    private $wished_collection_date;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $notified_at;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCompanyId(): ?int
    {
        return $this->company ? $this->company->getId() : null;
    }

    public function setCompanyId(int $company_id, EntityManager $entityManager): self
    {
        $company = $entityManager->find(CompanyModel::class, $company_id);
        if ($company) {
            $this->company = $company;
        } else {
            throw new \Exception("Company with ID $company_id not found.");
        }
        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function getProduct(): ?ProductModel

    {
        return $this->product;
    }

    public function setProductId(int $product_id, EntityManager $entityManager): self
    {
        $product = $entityManager->find(ProductModel::class, $product_id);
        if ($product) {
            $this->product = $product;
        } else {
            throw new \Exception("Product with ID $product_id not found.");
        }
        return $this;
    }

    public function getNotifiedQuantity(): ?int
    {
        return $this->notified_quantity;
    }

    public function setNotifiedQuantity(int $notified_quantity): self
    {
        $this->notified_quantity = $notified_quantity;
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

    public function getWishedCollectionDate(): ?\DateTimeInterface
    {
        return $this->wished_collection_date;
    }

    public function setWishedCollectionDate(\DateTimeInterface $wished_collection_date): self
    {
        $this->wished_collection_date = $wished_collection_date;
        return $this;
    }

    public function getNotifiedAt(): ?\DateTimeInterface
    {
        return $this->notified_at;
    }

    public function setNotifiedAt(\DateTimeInterface $notified_at): self
    {
        $this->notified_at = $notified_at;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'company_name' => $this->company ? $this->company->getName(): null,
            'product_id' => $this->product_id,
            'product_name' => $this->product ? $this->product->getName(): null,
            'notified_quantity' => $this->notified_quantity,
            'address' => $this->address,
            'wished_collection_date' => $this->wished_collection_date->format('Y-m-d H:i:s'),
            'notified_at' => $this->notified_at->format('Y-m-d H:i:s'),
        ];
    }
}
?>