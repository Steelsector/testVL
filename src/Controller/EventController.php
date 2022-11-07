<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Type;
use App\Repository\EventRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EventController extends AbstractController
{
    #[Route('/api/v1/event/create', name: 'event.create', methods: 'POST')]
    public function create(Request $request, EventRepository $eventRepository, TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $eventDetails = $jsonData['details'] ?? null;

        if (is_null($jsonData) || is_null($eventDetails)) {
            throw new BadRequestHttpException('Required fields are not set');
        }
        $type = $typeRepository->findOneById($jsonData['type_id']);
        if (is_null($type)) {
            throw new BadRequestHttpException('Type does not exists');
        }

        $event = new Event();
        $event->setDetails($eventDetails);
        $event->setType($type);
        $event->setTimestamp(new \DateTimeImmutable());
        $event = $eventRepository->save($event);

        $jsonResponse = $normalizer->normalize($event, 'json', ['groups' => 'show_event']);

        return new JsonResponse([
            'event' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/event/{typeId}', name: 'event.list', defaults: ['typeId' => 0], methods: 'GET')]
    public function listEvent(int $typeId, EventRepository $eventRepository, TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {

        if($typeId != 0) {
            $type = $typeRepository->findOneById($typeId);
            $events = $eventRepository->findBy(["type"=>$type]);
        } else {
            $events = $eventRepository->findAll();
        }

        $jsonResponse = $normalizer->normalize($events, 'json', ['groups' => 'show_event']);

        return new JsonResponse([
            'events' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/{event}/update', name: 'event.update', methods: 'POST')]
    public function updateEvent(Event $event,Request $request, EventRepository $eventRepository,TypeRepository $typeRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $eventDetails = $jsonData['details'] ?? null;

        if (is_null($jsonData) || is_null($eventDetails)) {
            throw new BadRequestHttpException('Required fields are not set');
        }
        $type = $typeRepository->findOneById($jsonData['type_id']);
        if (is_null($type)) {
            throw new BadRequestHttpException('Type does not exists');
        }

        $event->setDetails($eventDetails);
        $event->setType($type);
        $event->setTimestamp(new \DateTimeImmutable());
        $event = $eventRepository->save($event);

        $jsonResponse = $normalizer->normalize($event, 'json', ['groups' => 'show_event']);

        return new JsonResponse([
            'event' => $jsonResponse,
            'code' => 200,
        ]);
    }

    #[Route('/api/v1/{event}/delete', name: 'event.delete', methods: 'DELETE')]
    public function deleteEvent(Event $event, EventRepository $eventRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $deleted = $eventRepository->remove($event);

        if (!$deleted) {
            throw new BadRequestHttpException('Something went wrong');
        }

        return new JsonResponse([
            'deleted' => 'true',
            'code' => 200,
        ]);
    }
}
