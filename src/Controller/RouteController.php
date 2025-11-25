<?php

namespace App\Controller;

use App\Application\DTO\RouteRequest;
use App\Application\Service\RouteCalculator; 
use App\Domain\Model\Route; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route as SymfonyRoute;
use Symfony\Component\Serializer\SerializerInterface;

class RouteController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RouteCalculator $routeCalculator
    ) {}

    #[SymfonyRoute('/', name: 'homepage')]
    public function index(): Response
    {
        return new Response('<h1>API is running</h1>');
    }

    #[SymfonyRoute('/api/v1/routes', methods: ['POST'])]
    public function createRoute(Request $request): JsonResponse
    {
        try {
            /** @var RouteRequest $routeRequest */
            $routeRequest = $this->serializer->deserialize(
                $request->getContent(), 
                RouteRequest::class, 
                'json'
            );

            $route = $this->routeCalculator->findShortestRoute(
                $routeRequest->fromStationId,
                $routeRequest->toStationId,
                $routeRequest->analyticCode
            );

            $jsonResponse = $this->serializer->serialize($route, 'json', ['groups' => 'route']);

            return new JsonResponse($jsonResponse, Response::HTTP_CREATED, [], true);

        } catch (\Exception $e) {
            $errorResponse = [
                'status' => 'error',
                'message' => $e->getMessage(), 
            ];
            $httpCode = str_contains($e->getMessage(), 'Station') ? Response::HTTP_NOT_FOUND : Response::HTTP_BAD_REQUEST;
            return new JsonResponse($errorResponse, $httpCode);
        }
    }
}
