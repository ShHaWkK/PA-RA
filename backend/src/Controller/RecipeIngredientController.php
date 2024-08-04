<?php
// Path: backend/src/Controller/RecipeIngredientController.php

namespace Controller;

use Entity\RecipeIngredientModel;
use Entity\RecipeModel;
use Entity\ProductModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class RecipeIngredientController
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
                    return $this->addRecipeIngredient($input);
                case 'GET':
                    if (isset($uriParts[1])) {
                        return $this->getRecipeIngredientById($uriParts[1]);
                    } else {
                        return $this->getAllRecipeIngredients();
                    }
                case 'PUT':
                    if (isset($uriParts[1])) {
                        return $this->updateRecipeIngredient($uriParts[1], $input);
                    }
                    http_response_code(400);
                    return ['error' => 'Recipe Ingredient ID not specified'];
                case 'DELETE':
                    if (isset($uriParts[1])) {
                        return $this->deleteRecipeIngredient($uriParts[1]);
                    }
                    http_response_code(400);
                    return ['error' => 'Recipe Ingredient ID not specified'];
                default:
                    http_response_code(405);
                    return ['error' => 'Method Not Allowed'];
            }
        } catch (\Exception $e) {
            error_log("Exception in processRequest: " . $e->getMessage());
            throw $e;
        }
    }

    public function addRecipeIngredient($data)
    {
        try {
            if (!isset($data['recipe_id']) || !isset($data['product_id']) || !isset($data['quantity_needed'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields for new recipe ingredient'];
            }

            $recipe = $this->entityManager->getRepository(RecipeModel::class)->find($data['recipe_id']);
            if (!$recipe) {
                http_response_code(400);
                return ['error' => 'Recipe not found'];
            }

            $product = $this->entityManager->getRepository(ProductModel::class)->find($data['product_id']);
            if (!$product) {
                http_response_code(400);
                return ['error' => 'Product not found'];
            }

            $recipeIngredient = new RecipeIngredientModel();
            $recipeIngredient->setRecipe($recipe);
            $recipeIngredient->setProduct($product);
            $recipeIngredient->setQuantityNeeded($data['quantity_needed']);

            $this->entityManager->persist($recipeIngredient);
            $this->entityManager->flush();

            return ['id' => $recipeIngredient->getId(), 'message' => 'Recipe ingredient added successfully'];
        } catch (\Exception $e) {
            error_log("Exception in addRecipeIngredient: " . $e->getMessage());
            throw $e;
        }
    }

    public function getRecipeIngredientById($id)
    {
        try {
            $recipeIngredient = $this->entityManager->getRepository(RecipeIngredientModel::class)->find($id);
            if (!$recipeIngredient) {
                http_response_code(404);
                return ['error' => 'Recipe ingredient not found'];
            }
            return json_decode($this->serializer->serialize($recipeIngredient, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getRecipeIngredientById: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateRecipeIngredient($id, $data)
    {
        try {
            $recipeIngredient = $this->entityManager->getRepository(RecipeIngredientModel::class)->find($id);
            if (!$recipeIngredient) {
                http_response_code(404);
                return ['error' => 'Recipe ingredient not found'];
            }

            if (isset($data['recipe_id'])) {
                $recipe = $this->entityManager->getRepository(RecipeModel::class)->find($data['recipe_id']);
                if ($recipe) {
                    $recipeIngredient->setRecipe($recipe);
                }
            }

            if (isset($data['product_id'])) {
                $product = $this->entityManager->getRepository(ProductModel::class)->find($data['product_id']);
                if ($product) {
                    $recipeIngredient->setProduct($product);
                }
            }

            if (isset($data['quantity_needed'])) {
                $recipeIngredient->setQuantityNeeded($data['quantity_needed']);
            }

            $this->entityManager->flush();

            return ['id' => $recipeIngredient->getId(), 'message' => 'Recipe ingredient updated successfully'];
        } catch (\Exception $e) {
            error_log("Exception in updateRecipeIngredient: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteRecipeIngredient($id)
    {
        try {
            $recipeIngredient = $this->entityManager->getRepository(RecipeIngredientModel::class)->find($id);
            if (!$recipeIngredient) {
                http_response_code(404);
                return ['error' => 'Recipe ingredient not found'];
            }

            $this->entityManager->remove($recipeIngredient);
            $this->entityManager->flush();

            return ['message' => 'Recipe ingredient deleted successfully'];
        } catch (\Exception $e) {
            error_log("Exception in deleteRecipeIngredient: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllRecipeIngredients()
    {
        try {
            $recipeIngredientRepository = $this->entityManager->getRepository(RecipeIngredientModel::class);
            $recipeIngredients = $recipeIngredientRepository->findAll();
            return json_decode($this->serializer->serialize($recipeIngredients, 'json'), true);
        } catch (\Exception $e) {
            error_log("Exception in getAllRecipeIngredients: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
