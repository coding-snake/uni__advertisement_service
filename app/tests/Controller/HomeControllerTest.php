<?php

/**
 * Home controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomeControllerTest.
 */
class HomeControllerTest extends WebTestCase
{
    /**
     * Test '/' route.
     */
    public function testHomePageRendersTemplate(): void
    {
        try {
            // given
            $client = static::createClient();

            // when
            $client->request('GET', '/');

            // then
            $this->assertResponseIsSuccessful();

            $responseContent = $client->getResponse()->getContent();

            $this->assertStringContainsString('<html', $responseContent);
            $this->assertStringContainsString('</html>', $responseContent);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
