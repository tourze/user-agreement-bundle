<?php

namespace UserAgreementBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

/**
 * 用户协议实体测试数据
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class ProtocolEntityFixtures extends Fixture
{
    public const PROTOCOL_REGISTER_REFERENCE = 'protocol-register';
    public const PROTOCOL_USAGE_REFERENCE = 'protocol-usage';
    public const PROTOCOL_PRIVACY_REFERENCE = 'protocol-privacy';
    public const PROTOCOL_SALE_PUSH_REFERENCE = 'protocol-sale-push';
    public const PROTOCOL_OLD_REGISTER_REFERENCE = 'protocol-old-register';
    public const PROTOCOL_FUTURE_PRIVACY_REFERENCE = 'protocol-future-privacy';

    public function load(ObjectManager $manager): void
    {
        // 1. 创建用户注册协议
        $registerProtocol = new ProtocolEntity();
        $registerProtocol->setType(ProtocolType::MEMBER_REGISTER);
        $registerProtocol->setTitle('用户注册协议');
        $registerProtocol->setVersion('v1.0.0');
        $registerProtocol->setContent($this->getRegisterProtocolContent());
        $registerProtocol->setValid(true);
        $registerProtocol->setRequired(true);
        $registerProtocol->setEffectiveTime(new \DateTimeImmutable('2024-01-01'));
        // 测试环境不设置PDF URL，避免路由测试问题
        // $registerProtocol->setPdfUrl('/files/protocols/register-v1.0.0.pdf');
        $manager->persist($registerProtocol);

        // 2. 创建用户使用协议
        $usageProtocol = new ProtocolEntity();
        $usageProtocol->setType(ProtocolType::MEMBER_USAGE);
        $usageProtocol->setTitle('用户使用协议');
        $usageProtocol->setVersion('v2.1.0');
        $usageProtocol->setContent($this->getUsageProtocolContent());
        $usageProtocol->setValid(true);
        $usageProtocol->setRequired(true);
        $usageProtocol->setEffectiveTime(new \DateTimeImmutable('2024-01-15'));
        // $usageProtocol->setPdfUrl('/files/protocols/usage-v2.1.0.pdf');
        $manager->persist($usageProtocol);

        // 3. 创建隐私协议
        $privacyProtocol = new ProtocolEntity();
        $privacyProtocol->setType(ProtocolType::PRIVACY);
        $privacyProtocol->setTitle('隐私保护协议');
        $privacyProtocol->setVersion('v3.0.0');
        $privacyProtocol->setContent($this->getPrivacyProtocolContent());
        $privacyProtocol->setValid(true);
        $privacyProtocol->setRequired(true);
        $privacyProtocol->setEffectiveTime(new \DateTimeImmutable('2024-02-01'));
        // $privacyProtocol->setPdfUrl('/files/protocols/privacy-v3.0.0.pdf');
        $manager->persist($privacyProtocol);

        // 4. 创建营销推送协议（非必需）
        $salePushProtocol = new ProtocolEntity();
        $salePushProtocol->setType(ProtocolType::SALE_PUSH);
        $salePushProtocol->setTitle('营销推送服务协议');
        $salePushProtocol->setVersion('v1.2.0');
        $salePushProtocol->setContent($this->getSalePushProtocolContent());
        $salePushProtocol->setValid(true);
        $salePushProtocol->setRequired(false); // 营销推送非必需
        $salePushProtocol->setEffectiveTime(new \DateTimeImmutable('2024-03-01'));
        $manager->persist($salePushProtocol);

        // 5. 创建旧版本协议（已失效）
        $oldRegisterProtocol = new ProtocolEntity();
        $oldRegisterProtocol->setType(ProtocolType::MEMBER_REGISTER);
        $oldRegisterProtocol->setTitle('用户注册协议（旧版）');
        $oldRegisterProtocol->setVersion('v0.9.0');
        $oldRegisterProtocol->setContent('这是旧版本的用户注册协议内容...');
        $oldRegisterProtocol->setValid(false); // 已失效
        $oldRegisterProtocol->setRequired(true);
        $oldRegisterProtocol->setEffectiveTime(new \DateTimeImmutable('2023-01-01'));
        $manager->persist($oldRegisterProtocol);

        // 6. 创建即将生效的协议
        $futureProtocol = new ProtocolEntity();
        $futureProtocol->setType(ProtocolType::PRIVACY);
        $futureProtocol->setTitle('隐私保护协议（新版）');
        $futureProtocol->setVersion('v4.0.0');
        $futureProtocol->setContent($this->getFuturePrivacyProtocolContent());
        $futureProtocol->setValid(true);
        $futureProtocol->setRequired(true);
        $futureProtocol->setEffectiveTime(new \DateTimeImmutable('+30 days'));
        // $futureProtocol->setPdfUrl('/files/protocols/privacy-v4.0.0.pdf');
        $manager->persist($futureProtocol);

        $manager->flush();

        // 设置引用，供其他Fixture使用
        $this->addReference(self::PROTOCOL_REGISTER_REFERENCE, $registerProtocol);
        $this->addReference(self::PROTOCOL_USAGE_REFERENCE, $usageProtocol);
        $this->addReference(self::PROTOCOL_PRIVACY_REFERENCE, $privacyProtocol);
        $this->addReference(self::PROTOCOL_SALE_PUSH_REFERENCE, $salePushProtocol);
        $this->addReference(self::PROTOCOL_OLD_REGISTER_REFERENCE, $oldRegisterProtocol);
        $this->addReference(self::PROTOCOL_FUTURE_PRIVACY_REFERENCE, $futureProtocol);
    }

    private function getRegisterProtocolContent(): string
    {
        return <<<'HTML'
            <h3>用户注册协议</h3>
            <p><strong>生效日期：2024年1月1日</strong></p>

            <h4>1. 服务条款的接受</h4>
            <p>欢迎您使用我们的服务。通过注册账户，您同意遵守本协议的所有条款和条件。如果您不同意本协议的任何部分，请不要注册或使用我们的服务。</p>

            <h4>2. 账户注册</h4>
            <p>2.1 您必须提供真实、准确、最新和完整的个人信息。</p>
            <p>2.2 您有责任维护账户密码的机密性，并对在您账户下发生的所有活动负责。</p>
            <p>2.3 您必须年满18岁才能注册账户。</p>

            <h4>3. 用户行为准则</h4>
            <p>使用我们的服务时，您同意不会：</p>
            <ul>
            <li>违反任何适用的法律法规</li>
            <li>侵犯他人的知识产权或其他权利</li>
            <li>发布虚假、误导性或有害的内容</li>
            <li>干扰或破坏服务的正常运行</li>
            </ul>

            <h4>4. 隐私保护</h4>
            <p>我们重视您的隐私。请查阅我们的隐私政策，了解我们如何收集、使用和保护您的个人信息。</p>

            <h4>5. 服务的修改和终止</h4>
            <p>我们保留随时修改或终止服务的权利，恕不另行通知。我们不对您或任何第三方因服务修改或终止而造成的损失负责。</p>

            <h4>6. 免责声明</h4>
            <p>服务按"现状"提供，不提供任何明示或暗示的保证。</p>

            <h4>7. 协议的修改</h4>
            <p>我们可能会不时更新本协议。更新后的协议将在网站上发布，继续使用服务即表示您接受修改后的协议。</p>
            HTML;
    }

    private function getUsageProtocolContent(): string
    {
        return <<<'HTML'
            <h3>用户使用协议</h3>
            <p><strong>版本：v2.1.0 | 生效日期：2024年1月15日</strong></p>

            <h4>第一章 总则</h4>
            <p>1.1 本协议是您与本平台之间关于使用平台服务所订立的协议。</p>
            <p>1.2 本平台有权根据需要不时地制定、修改本协议或各项规则。</p>

            <h4>第二章 服务内容</h4>
            <p>2.1 本平台提供的服务包括但不限于：</p>
            <ul>
            <li>信息发布服务</li>
            <li>交易撮合服务</li>
            <li>支付结算服务</li>
            <li>数据分析服务</li>
            </ul>

            <h4>第三章 用户权利义务</h4>
            <p>3.1 用户有权按照本协议约定使用平台提供的各项服务。</p>
            <p>3.2 用户应当遵守法律法规，不得利用平台从事违法违规活动。</p>
            <p>3.3 用户应当对其发布的内容负责，确保内容的真实性、合法性。</p>

            <h4>第四章 平台权利义务</h4>
            <p>4.1 平台应当按照协议约定向用户提供服务。</p>
            <p>4.2 平台有权对违反协议的用户采取相应措施。</p>

            <h4>第五章 知识产权</h4>
            <p>5.1 平台上的所有内容的知识产权归属按照法律规定确定。</p>
            <p>5.2 用户不得侵犯平台或他人的知识产权。</p>

            <h4>第六章 争议解决</h4>
            <p>6.1 本协议的订立、执行和解释及争议的解决均应适用中华人民共和国法律。</p>
            <p>6.2 如双方就本协议内容或其执行发生任何争议，双方应友好协商解决。</p>
            HTML;
    }

    private function getPrivacyProtocolContent(): string
    {
        return <<<'HTML'
            <h3>隐私保护协议</h3>
            <p><strong>版本：v3.0.0 | 更新日期：2024年2月1日</strong></p>

            <h4>引言</h4>
            <p>我们深知个人信息对您的重要性，并会尽全力保护您的个人信息安全可靠。我们致力于维护您对我们的信任，恪守以下原则，保护您的个人信息：</p>

            <h4>1. 我们收集的信息</h4>
            <p>我们可能收集以下类型的信息：</p>
            <ul>
            <li><strong>账户信息：</strong>用户名、电子邮件地址、电话号码</li>
            <li><strong>个人资料：</strong>姓名、性别、出生日期、地址</li>
            <li><strong>设备信息：</strong>设备型号、操作系统、唯一设备标识符</li>
            <li><strong>使用数据：</strong>访问日志、搜索查询、交互信息</li>
            <li><strong>位置信息：</strong>基于GPS或IP地址的位置数据</li>
            </ul>

            <h4>2. 我们如何使用您的信息</h4>
            <p>2.1 提供、维护和改进我们的服务</p>
            <p>2.2 与您沟通，包括发送服务相关的通知</p>
            <p>2.3 个性化您的体验</p>
            <p>2.4 保护用户和平台的安全</p>
            <p>2.5 遵守法律义务</p>

            <h4>3. 信息的存储和安全</h4>
            <p>3.1 我们使用符合业界标准的安全防护措施保护您提供的个人信息。</p>
            <p>3.2 您的个人信息将存储在安全的服务器上。</p>
            <p>3.3 我们会定期审查和更新安全措施。</p>

            <h4>4. 信息的共享和披露</h4>
            <p>我们不会出售、出租或以其他方式分享您的个人信息，除非：</p>
            <ul>
            <li>获得您的明确同意</li>
            <li>法律法规要求</li>
            <li>保护我们的权利、财产或安全</li>
            <li>与我们的关联公司共享，用于提供服务</li>
            </ul>

            <h4>5. 您的权利</h4>
            <p>5.1 访问和更正您的个人信息</p>
            <p>5.2 删除您的个人信息</p>
            <p>5.3 限制或反对处理您的个人信息</p>
            <p>5.4 数据可携带性</p>
            <p>5.5 撤回同意</p>

            <h4>6. Cookie和类似技术</h4>
            <p>我们使用Cookie和类似技术来增强用户体验、分析使用情况和提供个性化内容。</p>

            <h4>7. 未成年人保护</h4>
            <p>我们非常重视对未成年人个人信息的保护。如果您是未成年人，请在您的父母或监护人的指导下使用我们的服务。</p>

            <h4>8. 隐私政策的更新</h4>
            <p>我们可能适时修订本隐私政策。如果我们对隐私政策做出重大变更，我们将通过网站公告或向您发送电子邮件等方式通知您。</p>

            <h4>9. 联系我们</h4>
            <p>如果您对本隐私政策有任何疑问或建议，请通过以下方式联系我们：</p>
            <p>电子邮件：privacy@test.example</p>
            HTML;
    }

    private function getSalePushProtocolContent(): string
    {
        return <<<'HTML'
            <h3>营销推送服务协议</h3>
            <p><strong>版本：v1.2.0 | 生效日期：2024年3月1日</strong></p>

            <h4>1. 服务说明</h4>
            <p>营销推送服务是我们为用户提供的个性化信息推送服务，包括但不限于：</p>
            <ul>
            <li>产品推荐和促销信息</li>
            <li>新功能和服务通知</li>
            <li>个性化的内容推荐</li>
            <li>市场调研邀请</li>
            </ul>

            <h4>2. 用户选择</h4>
            <p>2.1 本服务为可选服务，您可以选择是否接收营销推送。</p>
            <p>2.2 您可以随时通过账户设置更改您的推送偏好。</p>
            <p>2.3 即使您选择不接收营销推送，您仍会收到与账户和服务相关的重要通知。</p>

            <h4>3. 推送频率和时间</h4>
            <p>3.1 我们会合理控制推送频率，避免对您造成骚扰。</p>
            <p>3.2 推送时间一般为工作日9:00-21:00。</p>
            <p>3.3 重要促销活动期间，推送频率可能会适当增加。</p>

            <h4>4. 个性化推送</h4>
            <p>4.1 我们会基于您的使用行为和偏好提供个性化的推送内容。</p>
            <p>4.2 您可以设置您感兴趣的内容类别。</p>

            <h4>5. 退订方式</h4>
            <p>您可以通过以下方式退订营销推送：</p>
            <ul>
            <li>在推送消息中点击"退订"链接</li>
            <li>在账户设置中关闭营销推送</li>
            <li>联系客服申请退订</li>
            </ul>

            <h4>6. 数据使用</h4>
            <p>6.1 我们会记录您与推送消息的互动情况，用于改进服务。</p>
            <p>6.2 您的个人信息将按照隐私政策进行保护。</p>
            HTML;
    }

    private function getFuturePrivacyProtocolContent(): string
    {
        return <<<'HTML'
            <h3>隐私保护协议（新版）</h3>
            <p><strong>版本：v4.0.0 | 生效日期：30天后</strong></p>
            <p><em>本协议将于30天后正式生效，届时将替代现行的v3.0.0版本。</em></p>

            <h4>重要更新说明</h4>
            <p>相比v3.0.0版本，本次更新主要包括：</p>
            <ul>
            <li>增加了对生物识别信息的保护条款</li>
            <li>更新了数据跨境传输的规定</li>
            <li>加强了对第三方数据处理者的管理要求</li>
            <li>新增了自动化决策和用户画像的相关权利</li>
            </ul>

            <h4>1. 适用范围</h4>
            <p>本隐私政策适用于我们提供的所有产品和服务，包括网站、移动应用、API等。</p>

            <h4>2. 定义</h4>
            <p><strong>个人信息：</strong>是指以电子或者其他方式记录的能够单独或者与其他信息结合识别特定自然人身份的各种信息。</p>
            <p><strong>敏感个人信息：</strong>包括生物识别、宗教信仰、特定身份、医疗健康、金融账户、行踪轨迹等信息。</p>

            <h4>3. 个人信息的收集</h4>
            <p>3.1 我们遵循合法、正当、必要和诚信原则收集个人信息。</p>
            <p>3.2 对于敏感个人信息，我们会征得您的单独同意。</p>
            <p>3.3 我们会明确告知您收集信息的目的、方式和范围。</p>

            <h4>4. 生物识别信息保护</h4>
            <p>4.1 如果我们的服务涉及收集您的生物识别信息（如面部识别、指纹等），我们会：</p>
            <ul>
            <li>征得您的明示同意</li>
            <li>采用加密存储和传输</li>
            <li>限制访问权限</li>
            <li>定期进行安全审计</li>
            </ul>

            <h4>5. 数据跨境传输</h4>
            <p>5.1 如需将您的个人信息传输至境外，我们会：</p>
            <ul>
            <li>事先告知您接收方的名称、联系方式、处理目的、处理方式等</li>
            <li>取得您的单独同意</li>
            <li>采取必要措施确保境外接收方的保护水平不低于本政策的要求</li>
            </ul>

            <h4>6. 自动化决策</h4>
            <p>6.1 如果我们使用自动化决策技术（包括用户画像），您有权：</p>
            <ul>
            <li>了解决策的逻辑</li>
            <li>拒绝仅通过自动化决策的方式作出对您有重大影响的决定</li>
            <li>要求我们提供人工复核</li>
            </ul>

            <h4>7. 个人信息保护影响评估</h4>
            <p>对于高风险的个人信息处理活动，我们会事先进行个人信息保护影响评估。</p>

            <h4>8. 联系方式</h4>
            <p>个人信息保护负责人：privacy@test.example</p>
            <p>数据保护官：dpo@test.example</p>
            HTML;
    }
}
