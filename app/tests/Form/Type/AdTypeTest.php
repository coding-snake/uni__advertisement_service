<?php

/**
 * Ad type functional tests.
 */

namespace App\Tests\Form\Type;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Form\Type\AdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class AdTypeTest.
 */
class AdTypeTest extends KernelTestCase
{
    /**
     * Entity manager.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Form factory.
     */
    private ?FormFactoryInterface $formFactory;

    /**
     * Set up test.
     */
    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->formFactory = $container->get('form.factory');
    }

    /**
     * Test submit valid data.
     */
    public function testSubmitValidData(): void
    {
        // given
        $topic = new Topic();
        $topic->setName('Symfony');

        $tag = new Tag();
        $tag->setName('PHP');

        $this->entityManager->persist($topic);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $ad = new Ad();
        $form = $this->formFactory->create(AdType::class, $ad);

        // when
        $form->submit([
            'name' => 'name',
            'content' => 'content',
            'topic' => (string) $topic->getId(),
            'tags' => [(string) $tag->getId()],
        ]);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('name', $ad->getName());
        $this->assertEquals('content', $ad->getContent());
        $this->assertEquals($topic, $ad->getTopic());

        $this->assertCount(1, $ad->getTags());
        $this->assertTrue($ad->getTags()->contains($tag));
    }
}