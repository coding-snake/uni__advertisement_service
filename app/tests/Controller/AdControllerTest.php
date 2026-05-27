<?php
/**
 * Ad controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AdControllerTest.
 */
class AdControllerTest extends WebTestCase
{
    /**
     * Test '/ads' route.
     */
    public function testAdRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/ads');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }
}
