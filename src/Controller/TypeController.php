<?php
namespace App\Controller;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class TypeController extends AbstractController
{
    #[Route('/api/v1/type/create', name: 'type.create', methods: 'POST')]
    public function create(Request $request, TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $typeName = $jsonData['name'] ?? null;

        if (is_null($jsonData) || is_null($typeName)) {
            throw new BadRequestHttpException('Required fields are not set');
        }

        $type = new Type();
        $type->setName($typeName);
        $type = $typeRepository->save($type);

        $jsonResponse = $normalizer->normalize($type, 'json');

        return new JsonResponse([
            'type' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/type/{id}', name: 'type.get', methods: 'POST')]
    public function getType(Type $type,  NormalizerInterface $normalizer): JsonResponse
    {
        $jsonResponse = $normalizer->normalize($type, 'json');

        return new JsonResponse([
            'type' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/type', name: 'type.list', methods: 'GET')]
    public function listType(TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $types = $typeRepository->findAll();
        $jsonResponse = $normalizer->normalize($types, 'json');

        return new JsonResponse([
            'types' => $jsonResponse,
            'code' => 200,
        ]);

    }

    #[Route('/api/v1/type/{id}/update', name: 'type.update', methods: 'POST')]
    public function updateType(Type $type, Request $request, TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $typeName = $jsonData['name'] ?? null;

        if (is_null($jsonData) || is_null($typeName)) {
            throw new BadRequestHttpException('Required fields are not set');
        }

        $type->setName($typeName);
        $type = $typeRepository->save($type);

        $jsonResponse = $normalizer->normalize($type, 'json');

        return new JsonResponse([
            'type' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/type/{id}/delete', name: 'type.update', methods: 'DELETE')]
    public function deleteType(Type $type, Request $request, TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $deleted = $typeRepository->remove($type);
        if (!$deleted) {
            throw new BadRequestHttpException('Something went wrong');
        }

        return new JsonResponse([
            'deleted' => 'true',
            'code' => 200,
        ]);
    }

}