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
     * Test build form.
     */
    public function test_build_form(): void
    {
        try {
            // given
            $form_data = [
                'name' => 'tag_name',
            ];

            $tag = new Tag();
            $form = $this->factory->create(TagType::class, $tag);

            // when
            $form->submit($form_data);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertEquals('tag_name', $tag->getName());
            $this->assertEquals('tag_name', $form->get('name')->getData());
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
            $type = new TagType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolved_options = $resolver->resolve();
            $this->assertEquals(Tag::class, $resolved_options['data_class']);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test get block prefix.
     */
    public function test_get_block_prefix(): void
    {
        try {
            // given
            $type = new TagType();

            // when
            $result = $type->getBlockPrefix();

            // then
            $this->assertEquals('tag', $result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}