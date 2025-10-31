<?php

namespace UserAgreementBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Repository\AgreeLogRepository;

/**
 * @internal
 */
#[CoversClass(AgreeLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class AgreeLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Initialize test environment
    }

    protected function createNewEntity(): object
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_' . uniqid());
        $entity->setMemberId('test_member_' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): AgreeLogRepository
    {
        return self::getService(AgreeLogRepository::class);
    }

    #[Test]
    public function testSaveAndRemove(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol');
        $entity->setMemberId('test_member');
        $entity->setValid(true);

        $this->getRepository()->save($entity);
        $this->assertNotNull($entity->getId());

        // Store the ID before we lose the reference
        $savedId = $entity->getId();

        $found = $this->getRepository()->find($savedId);
        $this->assertNotNull($found);
        $this->assertEquals('test_protocol', $found->getProtocolId());

        $this->getRepository()->remove($entity);

        // Clear the entity manager to ensure changes are persisted
        self::getEntityManager()->clear();

        $removed = $this->getRepository()->find($savedId);
        $this->assertNull($removed);
    }

    #[Test]
    public function testFindByProtocolId(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_find');
        $entity->setMemberId('test_member_find');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['protocolId' => 'test_protocol_find']);
        $this->assertCount(1, $results);
        $this->assertEquals('test_protocol_find', $results[0]->getProtocolId());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByMemberId(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_member');
        $entity->setMemberId('test_member_unique');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['memberId' => 'test_member_unique']);
        $this->assertCount(1, $results);
        $this->assertEquals('test_member_unique', $results[0]->getMemberId());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testCountByValid(): void
    {
        $entity1 = new AgreeLog();
        $entity1->setProtocolId('test_protocol_count1');
        $entity1->setMemberId('test_member_count1');
        $entity1->setValid(true);

        $entity2 = new AgreeLog();
        $entity2->setProtocolId('test_protocol_count2');
        $entity2->setMemberId('test_member_count2');
        $entity2->setValid(false);

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $validCount = $this->getRepository()->count(['valid' => true]);
        $invalidCount = $this->getRepository()->count(['valid' => false]);

        $this->assertGreaterThanOrEqual(1, $validCount);
        $this->assertGreaterThanOrEqual(1, $invalidCount);

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testFindAll(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_all');
        $entity->setMemberId('test_member_all');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findAll();
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            if ('test_protocol_all' === $result->getProtocolId()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneBy(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_one');
        $entity->setMemberId('test_member_one');
        $entity->setValid(true);

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['protocolId' => 'test_protocol_one']);
        $this->assertNotNull($result);
        $this->assertEquals('test_protocol_one', $result->getProtocolId());
        $this->assertEquals('test_member_one', $result->getMemberId());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testSaveWithoutFlush(): void
    {
        $entity = new AgreeLog();
        $entity->setProtocolId('test_protocol_no_flush');
        $entity->setMemberId('test_member_no_flush');
        $entity->setValid(true);

        $this->getRepository()->save($entity, false);
        $this->assertNotNull($entity->getId());

        self::getService(EntityManagerInterface::class)->flush();

        $found = $this->getRepository()->find($entity->getId());
        $this->assertNotNull($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByOrderByShouldRespectOrderClause(): void
    {
        // 清理可能存在的测试数据
        $existingEntities = $this->getRepository()->findBy(['protocolId' => ['test_protocol_order_by_a', 'test_protocol_order_by_b']]);
        foreach ($existingEntities as $entity) {
            $this->getRepository()->remove($entity);
        }

        $entity1 = new AgreeLog();
        $entity1->setProtocolId('test_protocol_order_by_a');
        $entity1->setMemberId('test_member_order_by1');
        $entity1->setValid(true);

        $entity2 = new AgreeLog();
        $entity2->setProtocolId('test_protocol_order_by_b');
        $entity2->setMemberId('test_member_order_by2');
        $entity2->setValid(true);

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        // 测试 ASC 排序，应该返回 'a' 开头的
        $result = $this->getRepository()->findOneBy(['protocolId' => 'test_protocol_order_by_a'], ['protocolId' => 'ASC']);
        $this->assertInstanceOf(AgreeLog::class, $result);
        $this->assertSame('test_protocol_order_by_a', $result->getProtocolId());

        // 测试 DESC 排序，应该返回 'b' 开头的
        $result2 = $this->getRepository()->findOneBy(['protocolId' => 'test_protocol_order_by_b'], ['protocolId' => 'DESC']);
        $this->assertInstanceOf(AgreeLog::class, $result2);
        $this->assertSame('test_protocol_order_by_b', $result2->getProtocolId());

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }
}
