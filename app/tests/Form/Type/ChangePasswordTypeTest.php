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
     * Ad to pass the whole builder correctly
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
            'plainPassword' => [
                'first' => 'pass',
                'second' => 'pass',
            ],
        ];

        $form = $this->factory->create(ChangePasswordType::class);

        // when
        $form->submit($formData);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->has('plainPassword'));
        $this->assertEquals('pass', $form->get('plainPassword')->getData());
    }

    /**
     * Test conflicting passwords
     */
    public function testPasswordConflict(): void
    {
        // given
        $formData = [
            'plainPassword' => [
                'first' => 'pass',
                'second' => 'not',
            ],
        ];

        $form = $this->factory->create(ChangePasswordType::class);

        // when
        $form->submit($formData);

        // then
        $this->assertFalse($form->isValid());
    }

    /**
     * Test Configuration
     */
    public function testConfigureOptions(): void
    {
        // given
        $resolver = new OptionsResolver();
        $type = new ChangePasswordType();

        // when
        $type->configureOptions($resolver);

        // then
        $resolvedOptions = $resolver->resolve();
        $this->assertIsArray($resolvedOptions);
    }
}