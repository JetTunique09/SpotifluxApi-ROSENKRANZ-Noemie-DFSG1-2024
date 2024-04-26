<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ArtistController extends AbstractController
{
    public function __construct(
        private ArtistRepository $artistRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    )
    {
        
    }

    #[Route('/api/artist', name: 'app_api_artist', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $artist = $this->artistRepository->findAll();

        return $this->json([
            'artist' => $artist,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/artist/{id}', name: 'app_api_artist_get', methods: ['GET'])]
    public function get(?Artist $artist = null): JsonResponse
    {
        
        if(!$artist)
        {
            return $this->json([
                'error' => 'artist does not exist',
            ], 404);
        }

        return $this->json($artist, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/artist', name: 'app_api_artist_add', methods: ['POST'])]
    public function add( #[MapRequestPayload('json', ['groups' => ['create']])] Artist $artist
    ): JsonResponse
    {
        $this->em->persist($artist);
        $this->em->flush();

        return $this->json($artist, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/artist/{id}', name: 'app_api_artist_delete',  methods: ['DELETE'])]
    public function delete(?Artist $artist): JsonResponse
    {
        $this->em->remove($artist);
        $this->em->flush();

        return $this->json([
            'message' => 'artist deleted successfully'
        ], 200);
    }
}
