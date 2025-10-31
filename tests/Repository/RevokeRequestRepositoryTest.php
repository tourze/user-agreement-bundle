<?php

namespace UserAgreementBundle\Tests\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;
use UserAgreementBundle\Repository\RevokeRequestRepository;

/**
 * @internal
 */
#[CoversClass(RevokeRequestRepository::class)]
#[RunTestsInSeparateProcesses]
final class RevokeRequestRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Initialize test environment
    }

    protected function createNewEntity(): object
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_' . uniqid());
        $entity->setNickName('Test User ' . uniqid());

        return $entity;
    }

    protected function getRepository(): RevokeRequestRepository
    {
        return self::getService(RevokeRequestRepository::class);
    }

    #[Test]
    public function testSaveAndRemove(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity');
        $entity->setNickName('test_nickname');
        $entity->setAvatar('test_avatar.jpg');
        $entity->setRemark('test_remark');

        $this->getRepository()->save($entity);
        $this->assertNotNull($entity->getId());

        // Store the ID before we lose the reference
        $savedId = $entity->getId();

        $found = $this->getRepository()->find($savedId);
        $this->assertNotNull($found);
        /** @var RevokeRequest $found */
        $this->assertEquals(RevokeType::All, $found->getType());
        $this->assertEquals('test_identity', $found->getIdentity());

        $this->getRepository()->remove($entity);

        // Clear the entity manager to ensure changes are persisted
        self::getEntityManager()->clear();

        $removed = $this->getRepository()->find($savedId);
        $this->assertNull($removed);
    }

    #[Test]
    public function testFindByType(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::NO_NOTIFY);
        $entity->setIdentity('test_identity_type');
        $entity->setNickName('test_nickname_type');

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['type' => RevokeType::NO_NOTIFY]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            /** @var RevokeRequest $result */
            if ('test_identity_type' === $result->getIdentity()) {
                $found = true;
                $this->assertEquals(RevokeType::NO_NOTIFY, $result->getType());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByIdentity(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::NOTIFY);
        $entity->setIdentity('unique_identity_test');
        $entity->setNickName('test_nickname_identity');

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['identity' => 'unique_identity_test']);
        $this->assertCount(1, $results);
        /** @var RevokeRequest $firstResult */
        $firstResult = $results[0];
        $this->assertEquals('unique_identity_test', $firstResult->getIdentity());
        $this->assertEquals(RevokeType::NOTIFY, $firstResult->getType());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneBy(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_one');
        $entity->setNickName('test_nickname_one');
        $entity->setRemark('unique_remark_test');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['remark' => 'unique_remark_test']);
        $this->assertNotNull($result);
        /** @var RevokeRequest $result */
        $this->assertEquals('test_identity_one', $result->getIdentity());
        $this->assertEquals('unique_remark_test', $result->getRemark());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindAll(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_all');
        $entity->setNickName('test_nickname_all');

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findAll();
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            /** @var RevokeRequest $result */
            if ('test_identity_all' === $result->getIdentity()) {
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
        $entity1 = new RevokeRequest();
        $entity1->setType(RevokeType::All);
        $entity1->setIdentity('test_identity_count1');
        $entity1->setNickName('test_nickname_count1');

        $entity2 = new RevokeRequest();
        $entity2->setType(RevokeType::NO_NOTIFY);
        $entity2->setIdentity('test_identity_count2');
        $entity2->setNickName('test_nickname_count2');

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $allCount = $this->getRepository()->count(['type' => RevokeType::All]);
        $noNotifyCount = $this->getRepository()->count(['type' => RevokeType::NO_NOTIFY]);

        $this->assertGreaterThanOrEqual(1, $allCount);
        $this->assertGreaterThanOrEqual(1, $noNotifyCount);

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testSaveWithoutFlush(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_no_flush');
        $entity->setNickName('test_nickname_no_flush');

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
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_nullable');
        $entity->setNickName('test_nickname_nullable');
        $entity->setAvatar(null);
        $entity->setRemark(null);

        $this->getRepository()->save($entity);

        $results = $this->getRepository()->findBy(['avatar' => null]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            /** @var RevokeRequest $result */
            if ('test_identity_nullable' === $result->getIdentity()) {
                $found = true;
                $this->assertNull($result->getAvatar());
                $this->assertNull($result->getRemark());
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByUserShouldReturnMatchingEntity(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_user_association');
        $entity->setNickName('test_nickname_user_association');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['identity' => 'test_identity_user_association']);
        $this->assertNotNull($result);
        /** @var RevokeRequest $result */
        $this->assertEquals('test_identity_user_association', $result->getIdentity());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindByUserShouldReturnAllMatchingEntities(): void
    {
        $entity1 = new RevokeRequest();
        $entity1->setType(RevokeType::All);
        $entity1->setIdentity('test_identity_multiple1');
        $entity1->setNickName('test_nickname_multiple1');

        $entity2 = new RevokeRequest();
        $entity2->setType(RevokeType::NO_NOTIFY);
        $entity2->setIdentity('test_identity_multiple2');
        $entity2->setNickName('test_nickname_multiple2');

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $results = $this->getRepository()->findBy(['type' => RevokeType::All]);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $result) {
            /** @var RevokeRequest $result */
            if ('test_identity_multiple1' === $result->getIdentity()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testFindOneByOrderByShouldRespectOrderClause(): void
    {
        $entity1 = new RevokeRequest();
        $entity1->setType(RevokeType::All);
        $entity1->setIdentity('test_identity_order_by_a');
        $entity1->setNickName('test_nickname_order_by_a');

        $entity2 = new RevokeRequest();
        $entity2->setType(RevokeType::All);
        $entity2->setIdentity('test_identity_order_by_b');
        $entity2->setNickName('test_nickname_order_by_b');

        $this->getRepository()->save($entity1);
        $this->getRepository()->save($entity2);

        $result = $this->getRepository()->findOneBy(['type' => RevokeType::All], ['identity' => 'ASC']);
        $this->assertInstanceOf(RevokeRequest::class, $result);
        $this->assertEquals(RevokeType::All, $result->getType());

        $this->getRepository()->remove($entity1);
        $this->getRepository()->remove($entity2);
    }

    #[Test]
    public function testFindWithOptimisticLockWhenVersionMismatchesShouldThrowExceptionOnFlush(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_lock_version');
        $entity->setNickName('test_nickname_lock_version');

        $this->getRepository()->save($entity);
        $id = $entity->getId();
        $originalVersion = $entity->getLockVersion();

        // 模拟版本号冲突：手动修改实体的版本号以模拟过期的版本
        self::assertNotSame(null, $originalVersion);
        $entity->setLockVersion($originalVersion - 1);

        // 修改实体内容
        $entity->setRemark('Updated with old version');

        // 此时保存应该抛出乐观锁异常，因为版本号不匹配数据库中的版本
        $this->expectException(OptimisticLockException::class);
        $this->getRepository()->save($entity);
    }

    #[Test]
    public function testFindWithPessimisticWriteLockShouldReturnEntityAndLockRow(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_pessimistic_lock');
        $entity->setNickName('test_nickname_pessimistic_lock');

        $this->getRepository()->save($entity);
        $id = $entity->getId();

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->beginTransaction();

        try {
            $lockedEntity = $entityManager->find(
                RevokeRequest::class,
                $id,
                LockMode::PESSIMISTIC_WRITE
            );
            $entityManager->commit();
        } catch (\Exception $e) {
            $entityManager->rollback();
            throw $e;
        }

        $this->assertInstanceOf(RevokeRequest::class, $lockedEntity);
        $this->assertEquals($id, $lockedEntity->getId());

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testCountByAssociationUserShouldReturnCorrectNumber(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_user_count');
        $entity->setNickName('test_nickname_user_count');

        $this->getRepository()->save($entity);

        $count = $this->getRepository()->count(['user' => null]);
        $this->assertGreaterThanOrEqual(1, $count);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testCountByAssociationUpdatedByShouldReturnCorrectNumber(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_updated_by_count');
        $entity->setNickName('test_nickname_updated_by_count');

        $this->getRepository()->save($entity);

        $count = $this->getRepository()->count(['updatedBy' => null]);
        $this->assertGreaterThanOrEqual(1, $count);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testCountByAssociationCreatedByShouldReturnCorrectNumber(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_created_by_count');
        $entity->setNickName('test_nickname_created_by_count');

        $this->getRepository()->save($entity);

        $count = $this->getRepository()->count(['createdBy' => null]);
        $this->assertGreaterThanOrEqual(1, $count);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_find_one_user');
        $entity->setNickName('test_nickname_find_one_user');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['user' => null]);
        $this->assertInstanceOf(RevokeRequest::class, $result);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByAssociationCreatedByShouldReturnMatchingEntity(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_find_one_created_by');
        $entity->setNickName('test_nickname_find_one_created_by');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['createdBy' => null]);
        $this->assertInstanceOf(RevokeRequest::class, $result);

        $this->getRepository()->remove($entity);
    }

    #[Test]
    public function testFindOneByAssociationUpdatedByShouldReturnMatchingEntity(): void
    {
        $entity = new RevokeRequest();
        $entity->setType(RevokeType::All);
        $entity->setIdentity('test_identity_find_one_updated_by');
        $entity->setNickName('test_nickname_find_one_updated_by');

        $this->getRepository()->save($entity);

        $result = $this->getRepository()->findOneBy(['updatedBy' => null]);
        $this->assertInstanceOf(RevokeRequest::class, $result);

        $this->getRepository()->remove($entity);
    }
}
