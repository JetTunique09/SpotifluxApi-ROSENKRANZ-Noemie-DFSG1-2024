<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class TrackController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    )
    {
        
    }
    #[Route('/api/track', name: 'app_api_track', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $track = $this->trackRepository->findAll();

        return $this->json([
            'track' => $track,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_get', methods: ['GET'])]
    public function get(?Track $track = null): JsonResponse
    {
        
        if(!$track)
        {
            return $this->json([
                'error' => 'Track does not exist',
            ], 404);
        }

        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track', name: 'app_api_track_add', methods: ['POST'])]
    public function add( #[MapRequestPayload('json', ['groups' => ['create']])] Track $track
    ): JsonResponse
    {
        $this->em->persist($track);
        $this->em->flush();

        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_delete',  methods: ['DELETE'])]
    public function delete(Track $track): JsonResponse
    {
        $this->em->remove($track);
        $this->em->flush();

        return $this->json([
            'message' => 'Track deleted successfully'
        ], 200);
    }
}
