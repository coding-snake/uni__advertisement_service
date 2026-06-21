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
    private ?EntityManagerInterface $entity_manager;
    private ?FormFactoryInterface $form_factory;

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->form_factory = $container->get('form.factory');
    }

    /**
     * Test submit valid data.
     */
    public function test_submit_valid_data(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $tag = new Tag();
            $tag->setName('tag_name');

            $this->entity_manager->persist($topic);
            $this->entity_manager->persist($tag);
            $this->entity_manager->flush();

            $ad = new Ad();
            $form = $this->form_factory->create(AdType::class, $ad);

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