<?php
namespace Controller;

use Entity\RecipeModel;
use Entity\RecipeIngredientModel;
use Entity\ProductModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class RecipeController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ])];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processRequest($method, $uriParts, $input)
    {
        try {
            switch ($method) {
                case 'POST':
                    return $this->createRecipe($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        if ($uriParts[1] === 'suggest') {
                            return $this->suggestRecipes($input);
                        } else {
                            return $this->getRecipeById($uriParts[1]);
                        }
                    } else {
                        return $this->getAllRecipes();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateRecipe($uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Recipe ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteRecipe($uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Recipe ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function createRecipe($data)
    {
        try {
            if (!isset($data['name']) || !isset($data['instructions']) || !isset($data['ingredients'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new recipe'];
            }

            $recipe = new RecipeModel();
            $recipe->setName($data['name']);
            $recipe->setInstructions($data['instructions']);

            $this->entityManager->persist($recipe);
            $this->entityManager->flush();

            foreach ($data['ingredients'] as $ingredient) {
                $recipeIngredient = new RecipeIngredientModel();
                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setProduct($this->entityManager->getRepository(ProductModel::class)->find($ingredient['product_id']));
                $recipeIngredient->setQuantityNeeded($ingredient['quantity_needed']);

                $this->entityManager->persist($recipeIngredient);
            }

            $this->entityManager->flush();

            return ['id' => $recipe->getId(), 'message' => 'Recipe created successfully'];
        } catch (\Exception $e) {
            error_log("Exception in createRecipe: " . $e->getMessage());
            throw $e;
        }
    }

    public function getRecipeById($id)
    {
        try {
            $recipe = $this->entityManager->getRepository(RecipeModel::class)->find($id);
            if (!$recipe) {
                http_response_code(404);
                return ['error' => 'Recipe not found'];
            }
            return json_decode($this->serializer->serialize($recipe, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getRecipeById: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateRecipe($id, $data)
    {
        try {
            $recipe = $this->entityManager->getRepository(RecipeModel::class)->find($id);
            if (!$recipe) {
                http_response_code(404);
                return ['error' => 'Recipe not found'];
            }

            if (isset($data['name'])) {
                $recipe->setName($data['name']);
            }
            if (isset($data['instructions'])) {
                $recipe->setInstructions($data['instructions']);
            }

            $this->entityManager->flush();

            return ['id' => $recipe->getId(), 'message' => 'Recipe updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateRecipe: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteRecipe($id)
    {
        try {
            $recipe = $this->entityManager->getRepository(RecipeModel::class)->find($id);
            if (!$recipe) {
                http_response_code(404);
                return ['error' => 'Recipe not found'];
            }

            $this->entityManager->remove($recipe);
            $this->entityManager->flush();

            return ['message' => 'Recipe deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteRecipe: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllRecipes()
    {
        try {
            $recipeRepository = $this->entityManager->getRepository(RecipeModel::class);
            $recipes = $recipeRepository->findAll();
            return json_decode($this->serializer->serialize($recipes, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllRecipes: " . $e->getMessage());
            throw $e;
        }
    }

    public function suggestRecipes($input)
    {
        try {
            if (is_null($input)) {
                http_response_code(400);
                return ['error' => 'Input data is null'];
            }

            error_log("Starting suggestRecipes with input: " . json_encode($input));
            $productsInStock = $input['products_in_stock'];
            $recipes = $this->entityManager->getRepository(RecipeModel::class)->findAll();

            $suggestedRecipes = [];
            foreach ($recipes as $recipe) {
                $ingredients = $recipe->getIngredients();
                $canMakeRecipe = true;
                foreach ($ingredients as $ingredient) {
                    if (!isset($productsInStock[$ingredient->getProduct()->getId()]) ||
                        $productsInStock[$ingredient->getProduct()->getId()] < $ingredient->getQuantityNeeded()) {
                        $canMakeRecipe = false;
                        break;
                    }
                }
                if ($canMakeRecipe) {
                    $suggestedRecipes[] = $recipe;
                }
            }

            $response = json_decode($this->serializer->serialize($suggestedRecipes, 'json'), true);
            error_log("suggestRecipes response: " . json_encode($response));
            return $response;
        } catch (\Exception $e) {
            error_log("Exception in suggestRecipes: " . $e->getMessage());
            throw $e;
        }
    }

?>
