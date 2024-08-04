<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "recipe_ingredients")]
class RecipeIngredientModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: RecipeModel::class, inversedBy: "ingredients")]
    #[ORM\JoinColumn(nullable: false)]
    private $recipe;

    #[ORM\ManyToOne(targetEntity: ProductModel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\Column(type: "integer")]
    private $quantity_needed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?RecipeModel
    {
        return $this->recipe;
    }

    public function setRecipe(?RecipeModel $recipe): self
    {
        $this->recipe = $recipe;
        return $this;
    }

    public function getProduct(): ?ProductModel
    {
        return $this->product;
    }

    public function setProduct(?ProductModel $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantityNeeded(): ?int
    {
        return $this->quantity_needed;
    }

    public function setQuantityNeeded(int $quantity_needed): self
    {
        $this->quantity_needed = $quantity_needed;
        return $this;
    }
}
?>
