<?php

/**
 * User type tests.
 */

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Class UserTypeTest.
 */
class UserTypeTest extends TypeTestCase
{
    /**
     * Add to ensure constraints are handled correctly.
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
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => [
                'first' => 'secret123',
                'second' => 'secret123',
            ],
        ];

        $user = new User();
        $form = $this->factory->create(UserType::class, $user);

        // when
        $form->submit($formData);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('testuser', $user->getUsername());

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));

        $this->assertEquals(64, $form->get('username')->getConfig()->getOption('attr')['max_length']);
        $this->assertEquals('label.email', $form->get('email')->getConfig()->getOption('label'));
    }

    /**
     * Test validation failure on password mismatch
     */
    public function testPasswordMismatch(): void
    {
        // given
        $formData = [
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => [
                'first' => 'pass',
                'second' => 'not',
            ],
        ];

        $form = $this->factory->create(UserType::class, new User());

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
        $resolver = new OptionsResolver();
        $type = new UserType();
        $type->configureOptions($resolver);

        $resolvedOptions = $resolver->resolve();
        $this->assertEquals(User::class, $resolvedOptions['data_class']);
    }

    /**
     * Test Block Prefix
     */
    public function testGetBlockPrefix(): void
    {
        $type = new UserType();
        $this->assertEquals('user', $type->getBlockPrefix());
    }
}