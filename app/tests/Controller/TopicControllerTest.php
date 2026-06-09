<?php

namespace App\Tests\Controller;

use App\Entity\Topic;
use App\Entity\User;
use App\Service\TopicServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TopicControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;
    private TopicServiceInterface&MockObject $topicServiceMock;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        $this->topicServiceMock = $this->createMock(TopicServiceInterface::class);
        static::getContainer()->set(TopicServiceInterface::class, $this->topicServiceMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testIndex(): void
    {
        // given
        $paginationMock = $this->createMock(PaginationInterface::class);
        
        $this->topicServiceMock->expects($this->once())
            ->method('getPaginatedList')
            ->with(1)
            ->willReturn($paginationMock);

        // when
        $this->client->request('GET', '/topics');

        // then
        $this->assertResponseIsSuccessful();
    }

    public function testRead(): void
    {
        // given
        $topic = new Topic();
        $topic->setName('Test read');
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        // when
        $this->client->request('GET', '/topics/' . $topic->getId());

        // then
        $this->assertResponseIsSuccessful();
    }

    public function testCreate(): void
    {
        // given
        $user = $this->createUserAndLogin();
        $crawler = $this->client->request('GET', '/topics/create');

        $this->topicServiceMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Topic::class));

        // when
        $form = $crawler->filter('form')->form([
            'topic[name]' => 'Mocked New Topic',
        ]);
        $this->client->submit($form);

        // then
        $this->assertResponseRedirects('/topics');
    }

    public function testEdit(): void
    {
        // given
        $user = $this->createUserAndLogin();

        $topic = new Topic();
        $topic->setName('Old Topic');
        $topic->setAuthor($user);
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/topics/' . $topic->getId() . '/edit');

        $this->topicServiceMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Topic::class));

        // when
        $form = $crawler->filter('form')->form([
            'topic[name]' => 'Mocked Updated Topic',
        ]);
        $this->client->submit($form);

        // then
        $this->assertResponseRedirects('/topics');
    }

    public function testDelete(): void
    {
        // given
        $user = $this->createUserAndLogin();

        $topic = new Topic();
        $topic->setName('Topic to delete');
        $topic->setAuthor($user);
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $this->topicServiceMock->expects($this->once())
            ->method('canBeDeleted')
            ->with($this->isInstanceOf(Topic::class))
            ->willReturn(true);

        $this->topicServiceMock->expects($this->once())
            ->method('delete')
            ->with($this->isInstanceOf(Topic::class));

        $crawler = $this->client->request('GET', '/topics/' . $topic->getId() . '/delete');

        // when
        $form = $crawler->filter('form')->form();
        $this->client->submit($form);

        // then
        $this->assertResponseRedirects('/topics');
    }

    private function createUserAndLogin(): User
    {
        $user = new User();
        $user->setEmail('test_user_' . uniqid() . '@example.com');
        $user->setPassword('haslo123');
        $user->setRoles(['ROLE_ADMIN']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        return $user;
    }
}