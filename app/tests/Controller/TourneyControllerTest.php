<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Tourney;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;

class TourneyControllerTest extends WebTestCase
{
    private AbstractBrowser $client;
//    public function testResolveIndex()
//    {
//        $client = static::createClient();
//        $client->request('GET', '/');
//
//        self::assertResponseIsSuccessful();
//        self::assertSelectorTextContains('h2', 'Список турниров');
//        self::assertSelectorTextContains('button', 'Создать турнир');
//    }

//    public function testCreateTourney(): void
//    {
//        $this->createRandomTourney();
//        self::assertResponseStatusCodeSame(302);
//    }

//    public function testGetSingleTourney(): void
//    {
//        $this->createRandomTourney();
//        self::assertMatchesRegularExpression(
//            '/\/tourney\/\d+/',
//            $this->client->getResponse()->headers->get('location'),
//            'Неправильный URL турнира'
//        );
//        $crawler = $this->client->request('GET', $this->client->getResponse()->headers->get('location'));
//        self::assertResponseIsSuccessful();
//        self::assertCount(2, $crawler->filter('h3'));
//        self::assertCount(2, $crawler->filter('button'));
//    }

    private function createRandomTourney(): ?Crawler
    {
        $this->client = static::createClient();
        return $this->client->request('POST', '/');
    }
}