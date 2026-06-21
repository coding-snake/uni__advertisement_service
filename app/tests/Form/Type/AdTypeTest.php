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
    private ?EntityManagerInterface $entityManager;
    private ?FormFactoryInterface $formFactory;

    /**
     * Set up test.
     */
    protected function setUp(): void
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
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $tag = new Tag();
            $tag->setName('tag_name');

            $this->entityManager->persist($topic);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $ad = new Ad();
            $form = $this->formFactory->create(AdType::class, $ad);

            // when
            $form->submit([
                'name' => 'ad_name',
                'content' => 'ad_content',
                'topic' => (string) $topic->getId(),
                'tags' => [(string) $tag->getId()],
            ]);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertEquals('ad_name', $ad->getName());
            $this->assertEquals('ad_content', $ad->getContent());
            $this->assertEquals($topic, $ad->getTopic());

            $this->assertCount(1, $ad->getTags());
            $this->assertTrue($ad->getTags()->contains($tag));
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
