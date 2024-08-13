<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "collection_products")]
class CollectionProductModel
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CollectionModel::class)]
    #[ORM\JoinColumn(name: "collection_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $collection;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ProductNotificationModel::class)]
    #[ORM\JoinColumn(name: "notification_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $notification;

    #[ORM\Column(type: "integer")]
    private $quantity_collected;

    public function getCollection(): ?CollectionModel
    {
        return $this->collection;
    }

    public function setCollection(?CollectionModel $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function getNotification(): ?ProductNotificationModel
    {
        return $this->notification;
    }

    public function setNotification(?ProductNotificationModel $notification): self
    {
        $this->notification = $notification;
        return $this;
    }

    public function getQuantityCollected(): ?int
    {
        return $this->quantity_collected;
    }

    public function setQuantityCollected(int $quantity_collected): self
    {
        $this->quantity_collected = $quantity_collected;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'collection_id' => $this->collection->getId(),
            'notification_id' => $this->notification->getId(),
            'quantity_collected' => $this->quantity_collected,
        ];
    }
}
?>
