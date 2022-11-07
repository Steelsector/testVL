<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\Entity\Event;
use App\Entity\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class EventTest extends KernelTestCase
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    private $databaseTool;
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @test
     */
    public function an_event_can_be_created_in_the_database()
    {

        //Set up
        $event = new Event();

        $date = new \DateTimeImmutable();

        $event->setDetails("Log details");
        $event->setTimestamp($date);

        $typeRepository = $this->entityManager->getRepository(Type::class);
        $typeRecord = $typeRepository->findOneBy(['name' => 'info']);

        $event->setType($typeRecord);

        $this->entityManager->persist($event);
        //Do something
        $this->entityManager->flush();

        $eventRepository = $this->entityManager->getRepository(Event::class);
        $eventRecord = $eventRepository->findOneBy(['details'=> 'Log details']);

        // Make assertions
        $this->assertEquals('Log details', $eventRecord->getDetails());
        $this->assertEquals($typeRecord, $eventRecord->getType());
    }
}