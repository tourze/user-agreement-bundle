<?php

namespace UserAgreementBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use UserAgreementBundle\Controller\Admin\UserAgreementProtocolEntityCrudController;

/**
 * @internal
 */
#[CoversClass(UserAgreementProtocolEntityCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserAgreementProtocolEntityCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/user-agreement/protocol');
    }

    #[Test]
    public function testIndexPageAccessibleWithValidRole(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        $client->request('GET', '/admin/user-agreement/protocol');

        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testNewProtocolPageRequiresAuthentication(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/user-agreement/protocol/new');
    }

    #[Test]
    public function testNewProtocolFormValidation(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        $client->request('POST', '/admin/user-agreement/protocol/new', [
            'ProtocolEntity' => [
                'title' => '',
                'version' => '',
                'type' => '',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function testCreateProtocolFormAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        $client->request('GET', '/admin/user-agreement/protocol/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="ProtocolEntity"]');
    }

    #[Test]
    public function testSearchFunctionality(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        $client->request('GET', '/admin/user-agreement/protocol', [
            'query' => 'Test Protocol',
        ]);

        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testFilterFunctionality(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        $client->request('GET', '/admin/user-agreement/protocol', [
            'filters' => [
                'valid' => ['value' => true],
                'type' => ['value' => 'MEMBER_REGISTER'],
                'required' => ['value' => true],
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    #[Test]
    public function testDeleteActionIsNotSupported(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        // 在测试中捕获异常
        $client->catchExceptions(false);

        try {
            $client->request('DELETE', '/admin/user-agreement/protocol/1');
            self::fail('Expected MethodNotAllowedHttpException was not thrown');
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
            $this->assertEquals(405, $e->getStatusCode());
        }
    }

    #[Test]
    public function testCustomActionRouteNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());
        self::getClient($client);

        // 在测试中捕获异常
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/admin/user-agreement/protocol/1/duplicateVersion');
            self::fail('Expected NotFoundHttpException was not thrown');
        } catch (NotFoundHttpException $e) {
            $this->assertStringContainsString('No route found', $e->getMessage());
            $this->assertEquals(404, $e->getStatusCode());
        }
    }

    #[Test]
    public function testEditActionWithoutAuthenticationShouldRedirect(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/user-agreement/protocol/1/edit');
    }

    #[Test]
    public function testDetailActionWithoutAuthenticationShouldRedirect(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/user-agreement/protocol/1');
    }

    #[Test]
    public function testDeleteActionWithoutAuthenticationIsNotSupported(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/admin/user-agreement/protocol/1');
    }

    #[Test]
    public function testCustomActionWithoutAuthenticationNotFound(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);

        $this->expectException(NotFoundHttpException::class);
        $client->request('GET', '/admin/user-agreement/protocol/1/duplicateVersion');
    }

    protected function getControllerService(): UserAgreementProtocolEntityCrudController
    {
        return self::getService(UserAgreementProtocolEntityCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '是否有效' => ['是否有效'];
        yield '协议类型' => ['协议类型'];
        yield '协议名称' => ['协议名称'];
        yield '版本号' => ['版本号'];
        yield '是否必需' => ['是否必需'];
        yield '生效时间' => ['生效时间'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'valid' => ['valid'];
        yield 'type' => ['type'];
        yield 'title' => ['title'];
        yield 'version' => ['version'];
        yield 'content' => ['content'];
        yield 'pdfUrl' => ['pdfUrl'];
        yield 'required' => ['required'];
        yield 'effectiveTime' => ['effectiveTime'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'valid' => ['valid'];
        yield 'type' => ['type'];
        yield 'title' => ['title'];
        yield 'version' => ['version'];
        yield 'content' => ['content'];
        yield 'pdfUrl' => ['pdfUrl'];
        yield 'required' => ['required'];
        yield 'effectiveTime' => ['effectiveTime'];
    }
}
