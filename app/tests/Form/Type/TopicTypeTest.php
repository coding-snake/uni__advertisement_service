<?php
/**
 * Topic type tests.
 */

namespace App\Tests\Form\Type;

use App\Entity\Topic;
use App\Form\Type\TopicType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TopicTypeTest.
 */
class TopicTypeTest extends TypeTestCase
{
    /**
     * Test build form.
     */
    public function test_build_form(): void
    {
        try {
            // given
            $form_data = [
                'name' => 'topic_name',
            ];

            $topic = new Topic();
            $form = $this->factory->create(TopicType::class, $topic);

            // when
            $form->submit($form_data);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertEquals('topic_name', $topic->getName());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test configure options.
     */
    public function test_configure_options(): void
    {
        try {
            // given
            $resolver = new OptionsResolver();
            $type = new TopicType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolved_options = $resolver->resolve();
            $this->assertEquals(Topic::class, $resolved_options['data_class']);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}