<?php

/**
 * Username type tests.
 */

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UsernameType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

/**
 * Class UsernameTypeTest.
 */
class UsernameTypeTest extends TypeTestCase
{
    /**
     * Test build form.
     */
    public function testBuildForm(): void
    {
        try {
            // given
            $formData = [
                'username' => 'test',
            ];

            $user = new User();
            $form = $this->factory->create(UsernameType::class, $user);

            // when
            $form->submit($formData);

            // then
            $this->assertTrue($form->isSynchronized());
            $this->assertEquals('test', $user->getUsername());
            $this->assertTrue($form->has('username'));
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
            $type = new UsernameType();

            // when
            $type->configureOptions($resolver);

            // then
            $resolvedOptions = $resolver->resolve();
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
