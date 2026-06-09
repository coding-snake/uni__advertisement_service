<?php

namespace App\Tests\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdControllerTest extends WebTestCase
{
    /**
     * Testowanie ścieżki routowania, statusu 200 oraz renderowanego szablonu.
     */
    public function testIndexRouteReturnsSuccessfulResponse(): void
    {
        $client = static::createClient();
    
        $crawler = $client->request('GET', '/ads');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
    }

    /**
     * Testowanie dostępu - niezalogowany użytkownik nie ma dostępu do tworzenia ogłoszeń.
     */
    public function testCreateRouteRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ads/create');

        $this->assertResponseRedirects('/login');
    }

    /**
     * Testowanie dostępu - użytkownik z uprawnieniami (zalogowany) ma dostęp do metody create.
     */
    public function testCreateRouteIsAccessibleForLoggedUser(): void
    {
        $client = static::createClient();
        
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user@example.com');
        
        $client->loginUser($testUser);
        
        $client->request('GET', '/ads/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="ad"]'); 
    }

    /**
     * Testowanie konkretnej metody chronionej przez IsGranted('ROLE_ADMIN') i POST
     */
    public function testToggleVerificationRequiresAdminRole(): void
    {
        $client = static::createClient();
        
        $adRepository = static::getContainer()->get(AdRepository::class);
        /** @var Ad $ad */
        $ad = $adRepository->findAll()[0];
        $url = sprintf('/ads/%d/toggle-verification', $ad->getId());

        $userRepository = static::getContainer()->get(UserRepository::class);
        $regularUser = $userRepository->findOneByEmail('user@example.com');
        $client->loginUser($regularUser);

        $client->request('POST', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        
        $adminUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($adminUser);
        
        $client->request('POST', $url);

        $this->assertResponseRedirects('/ads/' . $ad->getId());
    }

    /**
     * Testowanie metody Delete i sprawdzenie Votera (AdVoter::DELETE).
     */
    public function testDeleteAdFailsForNonAuthor(): void
    {
        $client = static::createClient();
        
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adRepository = static::getContainer()->get(AdRepository::class);

        $ad = $adRepository->findOneBy(['verified' => true]); 
        
        $hackerUser = $userRepository->findOneByEmail('otheruser@example.com');
        $client->loginUser($hackerUser);

        $client->request('GET', sprintf('/ads/%d/delete', $ad->getId()));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}