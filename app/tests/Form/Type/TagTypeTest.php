<?php

/**
 * Tag type tests.
 */

namespace App\Tests\Form\Type;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Class TagTypeTest.
 */
class TagTypeTest extends TypeTestCase
{
    /**
     * Add to pass the whole builder correctly.
     */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    /**
     * Test Build Form
     */
    public function testBuildForm(): void
    {
        // given
        $formData = [
            'name' => 'tag',
        ];

        $tag = new Tag();
        $form = $this->factory->create(TagType::class, $tag);

        // when
        $form->submit($formData);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('tag', $tag->getName());
        $this->assertEquals('tag', $form->get('name')->getData());
    }

    /**
     * Test Configuration
     */
    public function testConfigureOptions(): void
    {
        // given
        $resolver = new OptionsResolver();
        $type = new TagType();

        // when
        $type->configureOptions($resolver);

        // then
        $resolvedOptions = $resolver->resolve();
        $this->assertEquals(Tag::class, $resolvedOptions['data_class']);
    }

    /**
     * Test Block Prefix
     */
    public function testGetBlockPrefix(): void
    {
        // given
        $type = new TagType();

        // when
        $result = $type->getBlockPrefix();

        // then
        $this->assertEquals('tag', $result);
    }
}