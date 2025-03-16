<?php

namespace App\Controller;

use App\Entity\FavoriteCity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/favorite-cities", name="api_favorite_cities_")
 */
class FavoriteCityController extends AbstractController
{
    private $entityManager;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $favoriteCities = $user->getFavoriteCities();
        
        $data = [];
        foreach ($favoriteCities as $city) {
            $data[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
                'countryCode' => $city->getCountryCode(),
                'createdAt' => $city->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['countryCode'])) {
            return new JsonResponse(['message' => 'Name and country code are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $favoriteCity = new FavoriteCity();
        $favoriteCity->setName($data['name']);
        $favoriteCity->setCountryCode($data['countryCode']);
        $favoriteCity->setUser($this->getUser());

        $errors = $this->validator->validate($favoriteCity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($favoriteCity);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $favoriteCity->getId(),
            'name' => $favoriteCity->getName(),
            'countryCode' => $favoriteCity->getCountryCode(),
            'createdAt' => $favoriteCity->getCreatedAt()->format('Y-m-d H:i:s')
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(FavoriteCity $favoriteCity): JsonResponse
    {
        if ($favoriteCity->getUser() !== $this->getUser()) {
            return new JsonResponse(['message' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($favoriteCity);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Favorite city removed successfully']);
    }
} 