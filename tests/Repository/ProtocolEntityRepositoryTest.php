<?php

namespace UserAgreementBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

/**
 * @internal
 */
#[CoversClass(ProtocolEntityRepository::class)]
#[RunTestsInSeparateProcesses]
final class ProtocolEntityRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Initialize test environment
    }

    protected function createNewEntity(): object
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::PRIVACY);
        $entity->setTitle('Test Protocol ' . uniqid());
        $entity->setVersion('1.0.' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): ProtocolEntityRepository
    {
        return self::getService(ProtocolEntityRepository::class);
    }

    #[Test]
    public function testSaveAndRemove(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::PRIVACY);
        $entity->setTitle('Test Protocol');
        $entity->setVersion('1.0.0');
        $entity->setValid(true);
        $entity->setContent('Test protocol content');
        $entity->setRequired(true);

        $this->getRepository()->save($entity);
        $this->assertNotNull($entity->getId());

        // Store the ID before we lose the reference
        $savedId = $entity->getId();

        $found = $this->getRepository()->find($savedId);
        $this->assertNotNull($found);
        $this->assertEquals(ProtocolType::PRIVACY, $found->getType());
        $this->assertEquals('Test Protocol', $found->getTitle());
        $this->assertEquals('1.0.0', $found->getVersion());

        $this->getRepository()->remove($entity);

        // Clear the entity manager to ensure changes are persisted
        self::getEntityManager()->clear();

        $removed = $this->getRepository()->find($savedId);
        $this->assertNull($removed);
    }

    #[Test]
    public function testFindByType(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::MEMBER_REGISTER);
        $entity->setTitle('Member Register Protocol');
        $entity->setVersion('2.0.0');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['type' => ProtocolType::MEMBER_REGISTER]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('Member Register Protocol' === $result->getTitle()) {
                $found = true;
                $this->assertEquals(ProtocolType::MEMBER_REGISTER, $result->getType());
                $this->assertEquals('2.0.0', $result->getVersion());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByValid(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::MEMBER_USAGE);
        $entity->setTitle('Valid Usage Protocol');
        $entity->setVersion('1.5.0');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['valid' => true]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('Valid Usage Protocol' === $result->getTitle()) {
                $found = true;
                $this->assertTrue($result->isValid());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByTitle(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::SALE_PUSH);
        $entity->setTitle('Unique Sale Push Protocol');
        $entity->setVersion('3.0.0');
        $entity->setValid(false);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['title' => 'Unique Sale Push Protocol']);
        $this->assertCount(1, $results);
        $this->assertEquals('Unique Sale Push Protocol', $results[0]->getTitle());
        $this->assertEquals(ProtocolType::SALE_PUSH, $results[0]->getType());
        $this->assertFalse($results[0]->isValid());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneBy(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::PRIVACY);
        $entity->setTitle('Privacy Policy Test');
        $entity->setVersion('unique-version-123');
        $entity->setValid(true);
        $entity->setContent('Privacy policy content for testing');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['version' => 'unique-version-123']);
        $this->assertNotNull($result);
        $this->assertEquals('Privacy Policy Test', $result->getTitle());
        $this->assertEquals('unique-version-123', $result->getVersion());
        $this->assertEquals('Privacy policy content for testing', $result->getContent());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindAll(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::MEMBER_REGISTER);
        $entity->setTitle('Find All Test Protocol');
        $entity->setVersion('findall-1.0');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findAll();
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('Find All Test Protocol' === $result->getTitle()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testCount(): void
    {
        $entity1 = new ProtocolEntity();
        $entity1->setType(ProtocolType::PRIVACY);
        $entity1->setTitle('Count Test Protocol 1');
        $entity1->setVersion('count-1.0');
        $entity1->setValid(true);

        $entity2 = new ProtocolEntity();
        $entity2->setType(ProtocolType::PRIVACY);
        $entity2->setTitle('Count Test Protocol 2');
        $entity2->setVersion('count-2.0');
        $entity2->setValid(false);

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $validCount = $this->getRepository()->count(['valid' => true]);
        $invalidCount = $this->getRepository()->count(['valid' => false]);
        $privacyCount = $this->getRepository()->count(['type' => ProtocolType::PRIVACY]);

        $this->assertGreaterThanOrEqual(1, $validCount);
        $this->assertGreaterThanOrEqual(1, $invalidCount);
        $this->assertGreaterThanOrEqual(2, $privacyCount);

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testSaveWithoutFlush(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::MEMBER_USAGE);
        $entity->setTitle('No Flush Test Protocol');
        $entity->setVersion('noflush-1.0');
        $entity->setValid(true);

        $this->getRepository()->save($entity, false);
        $this->assertNotNull($entity->getId());

        self::getService(EntityManagerInterface::class)->flush();

        $found = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByNullableFields(): void
    {
        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::PRIVACY);
        $entity->setTitle('Nullable Fields Test');
        $entity->setVersion('nullable-1.0');
        $entity->setValid(true);
        $entity->setContent(null);
        $entity->setPdfUrl(null);
        $entity->setRequired(null);
        $entity->setEffectiveTime(null);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['content' => null]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('Nullable Fields Test' === $result->getTitle()) {
                $found = true;
                $this->assertNull($result->getContent());
                $this->assertNull($result->getPdfUrl());
                $this->assertNull($result->isRequired());
                $this->assertNull($result->getEffectiveTime());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByRequired(): void
    {
        $entity1 = new ProtocolEntity();
        $entity1->setType(ProtocolType::MEMBER_REGISTER);
        $entity1->setTitle('Required Protocol');
        $entity1->setVersion('required-1.0');
        $entity1->setValid(true);
        $entity1->setRequired(true);

        $entity2 = new ProtocolEntity();
        $entity2->setType(ProtocolType::MEMBER_USAGE);
        $entity2->setTitle('Optional Protocol');
        $entity2->setVersion('optional-1.0');
        $entity2->setValid(true);
        $entity2->setRequired(false);

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $requiredResults = $this->getRepository()->findBy(['required' => true]);
        $optionalResults = $this->getRepository()->findBy(['required' => false]);

        $this->assertNotEmpty($requiredResults);
        $this->assertNotEmpty($optionalResults);

        $requiredFound = false;
        $optionalFound = false;

        foreach ($requiredResults as $result) {
            if ('Required Protocol' === $result->getTitle()) {
                $requiredFound = true;
                $this->assertTrue($result->isRequired());
                break;
            }
        }

        foreach ($optionalResults as $result) {
            if ('Optional Protocol' === $result->getTitle()) {
                $optionalFound = true;
                $this->assertFalse($result->isRequired());
                break;
            }
        }

        $this->assertTrue($requiredFound);
        $this->assertTrue($optionalFound);

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testFindByEffectiveTime(): void
    {
        $effectiveTime = new \DateTimeImmutable('2024-01-01 00:00:00');

        $entity = new ProtocolEntity();
        $entity->setType(ProtocolType::PRIVACY);
        $entity->setTitle('Effective Time Test');
        $entity->setVersion('effective-1.0');
        $entity->setValid(true);
        $entity->setEffectiveTime($effectiveTime);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['effectiveTime' => $effectiveTime]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('Effective Time Test' === $result->getTitle()) {
                $found = true;
                $this->assertEquals($effectiveTime, $result->getEffectiveTime());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByOrderByShouldRespectOrderClause(): void
    {
        $entity1 = new ProtocolEntity();
        $entity1->setType(ProtocolType::PRIVACY);
        $entity1->setTitle('Order By Test A');
        $entity1->setVersion('order-by-a-1.0');
        $entity1->setValid(true);

        $entity2 = new ProtocolEntity();
        $entity2->setType(ProtocolType::PRIVACY);
        $entity2->setTitle('Order By Test B');
        $entity2->setVersion('order-by-b-1.0');
        $entity2->setValid(true);

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $result = $this->getRepository()->findOneBy(['type' => ProtocolType::PRIVACY], ['title' => 'ASC']);
        $this->assertInstanceOf(ProtocolEntity::class, $result);
        $this->assertEquals(ProtocolType::PRIVACY, $result->getType());

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }
}
