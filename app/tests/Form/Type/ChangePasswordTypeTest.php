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
     * Test build form.
     */
    public function testBuildForm(): void
    {
        try {
            // given
            $formData = [
                'plainPassword' => [
                    'first' => 'password_1',
                    'second' => 'password_1',
                ],
            ];

            $form = $this->factory->create(ChangePasswordType::class);

            // when
            $form->submit($formData);

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
    public function testPasswordConflict(): void
    {
        try {
            // given
            $formData = [
                'plainPassword' => [
                    'first' => 'password_1',
                    'second' => 'password_2',
                ],
            ];

            $form = $this->factory->create(ChangePasswordType::class);

            // when
            $form->submit($formData);

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
    public function testConfigureOptions(): void
    {
        try {
            // given
            $resolver = new OptionsResolver();
            $type = new ChangePasswordType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolvedOptions = $resolver->resolve();
            $this->assertIsArray($resolvedOptions);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Add to pass the whole builder correctly.
     *
     * @return array<int, ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
