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
    public function testBuildForm(): void
    {
        try {
            // given
            $formData = [
                'name' => 'topic_name',
            ];

            $topic = new Topic();
            $form = $this->factory->create(TopicType::class, $topic);

            // when
            $form->submit($formData);

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
    public function testConfigureOptions(): void
    {
        try {
            // given
            $resolver = new OptionsResolver();
            $type = new TopicType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolvedOptions = $resolver->resolve();
            $this->assertEquals(Topic::class, $resolvedOptions['data_class']);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
