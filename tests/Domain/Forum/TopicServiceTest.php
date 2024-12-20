<?php

namespace App\Tests\Domain\Forum;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\TopicResolvedEvent;
use App\Domain\Forum\TopicService;
use App\Tests\FixturesTrait;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class TopicServiceTest extends KernelTestCase
{
    use FixturesTrait;

    private Topic $topic;
    private Topic $topic2;
    private User $user;
    private User $user2;
    private TopicService $service;

    public function setUp(): void
    {
        parent::setUp();
        $data = $this->loadFixtures(['forums']);
        $this->topic = $data['topic1'];
        $this->topic2 = $data['topic2'];
        $this->user = $data['user1'];
        $this->user2 = $data['user2'];
        $this->service = self::getContainer()->get(TopicService::class);
    }

    public function testGetReadTopicsIds(): void
    {
        $this->assertCount(
            0,
            $this->service->getReadTopicsIds([
                $this->topic,
                $this->topic2,
            ], $this->user)
        );
    }

    public function testGetReadTopicsIdsWithReadItems(): void
    {
        $this->service->readTopic($this->topic, $this->user);
        $this->em->flush();
        $this->assertEquals(
            [$this->topic->getId()],
            $this->service->getReadTopicsIds([
                $this->topic,
                $this->topic2,
            ], $this->user)
        );
        $this->assertCount(
            0,
            $this->service->getReadTopicsIds([
                $this->topic,
                $this->topic2,
            ], $this->user2)
        );
    }

    public function testGetReadTopicsIdsWithUserForumReadTime(): void
    {
        $this->user->setForumReadTime(new \DateTimeImmutable());
        $this->assertCount(
            2,
            $this->service->getReadTopicsIds([
                $this->topic,
                $this->topic2,
            ], $this->user)
        );
    }

    public function testReadAllTopics(): void
    {
        $this->service->readAllTopics($this->user);
        $this->assertCount(
            2,
            $this->service->getReadTopicsIds([
                $this->topic,
                $this->topic2,
            ], $this->user)
        );
    }

    public function testSolveEmitEvent(): void
    {
        $message = new Message();
        $topic = new Topic();
        $message->setTopic($topic);
        $em = $this->createMock(EntityManagerInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(TopicResolvedEvent::class));
        $service = new TopicService($dispatcher, $em);
        $service->messageSolveTopic($message);
        $this->assertTrue($topic->isSolved());
        $this->assertTrue($message->isAccepted());
    }
}
