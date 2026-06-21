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
    public function test_home_page_renders_template(): void
    {
        try {
            // given
            $client = static::createClient();

            // when
            $client->request('GET', '/');

            // then
            $this->assertResponseIsSuccessful();

            $response_content = $client->getResponse()->getContent();

            $this->assertStringContainsString('<html', $response_content);
            $this->assertStringContainsString('</html>', $response_content);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}