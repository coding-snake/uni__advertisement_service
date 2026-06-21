<?php
/**
 * Change password type tests.
 */

namespace App\Tests\Form\Type;

use App\Form\Type\ChangePasswordType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Class ChangePasswordTypeTest.
 */
class ChangePasswordTypeTest extends TypeTestCase
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
                'plainPassword' => [
                    'first' => 'password_1',
                    'second' => 'password_1',
                ],
            ];

            $form = $this->factory->create(ChangePasswordType::class);

            // when
            $form->submit($form_data);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertTrue($form->has('plainPassword'));
            $this->assertEquals('password_1', $form->get('plainPassword')->getData());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test password conflict.
     */
    public function test_password_conflict(): void
    {
        try {
            // given
            $form_data = [
                'plainPassword' => [
                    'first' => 'password_1',
                    'second' => 'password_2',
                ],
            ];

            $form = $this->factory->create(ChangePasswordType::class);

            // when
            $form->submit($form_data);

            // then
            $this->assertFalse($form->isValid());
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
            $type = new ChangePasswordType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolved_options = $resolver->resolve();
            $this->assertIsArray($resolved_options);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}