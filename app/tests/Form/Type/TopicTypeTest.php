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
     * Test Build Form
     */
    public function testBuildForm(): void
    {
        // given
        $formData = [
            'name' => 'topic',
        ];

        $topic = new Topic();
        $form = $this->factory->create(TopicType::class, $topic);

        // when
        $form->submit($formData);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('topic', $topic->getName());
    }

    /**
     * Test Configuration
     */
    public function testConfigureOptions(): void
    {
        // given
        $resolver = new OptionsResolver;
        $type = new TopicType();

        // when
        $type->configureOptions($resolver);

        // then
        $resolvedOptions = $resolver->resolve();
        $this->assertEquals(Topic::class, $resolvedOptions['data_class']);
    }
}