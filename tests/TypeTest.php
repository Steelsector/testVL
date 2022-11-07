<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Type;

class TypeTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
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
    public function a_type_can_be_created_in_the_database()
    {
        //Set up
        $type = new Type();

        $type->setName("debug");

        $this->entityManager->persist($type);
        //Do something
        $this->entityManager->flush();

        $typeRepository = $this->entityManager->getRepository(Type::class);

        $typeRecord = $typeRepository->findOneBy(['name' => 'debug']);

        // Make assertions
        $this->assertEquals('debug', $typeRecord->getName());
    }
}