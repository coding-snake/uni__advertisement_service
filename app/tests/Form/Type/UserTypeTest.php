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
     * Test build form.
     */
    public function testBuildForm(): void
    {
        try {
            // given
            $formData = [
                'email' => 'test@example.com',
                'username' => 'test',
                'password' => [
                    'first' => 'password_1',
                    'second' => 'password_1',
                ],
            ];

            $user = new User();
            $form = $this->factory->create(UserType::class, $user);

            // when
            $form->submit($formData);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertEquals('test@example.com', $user->getEmail());
            $this->assertEquals('test', $user->getUsername());

            $this->assertTrue($form->has('email'));
            $this->assertTrue($form->has('username'));
            $this->assertTrue($form->has('password'));

            $this->assertEquals(64, $form->get('username')->getConfig()->getOption('attr')['max_length']);
            $this->assertEquals('label.email', $form->get('email')->getConfig()->getOption('label'));
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test password mismatch.
     */
    public function testPasswordMismatch(): void
    {
        try {
            // given
            $formData = [
                'email' => 'test@example.com',
                'username' => 'test',
                'password' => [
                    'first' => 'password_1',
                    'second' => 'password_2',
                ],
            ];

            $form = $this->factory->create(UserType::class, new User());

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
            $type = new UserType();

            // when
            $type->configureOptions($resolver);
            $resolvedOptions = $resolver->resolve();

            // then
            $this->assertEquals(User::class, $resolvedOptions['data_class']);
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
    public function testGetBlockPrefix(): void
    {
        try {
            // given
            $type = new UserType();

            // when
            $result = $type->getBlockPrefix();

            // then
            $this->assertEquals('user', $result);
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
