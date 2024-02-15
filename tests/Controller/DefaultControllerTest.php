<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DefaultControllerTest extends WebTestCase
{
    public KernelBrowser $client;

    public function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    #[DataProvider('providerUrls')]
    public function testUrls(string $url)
    {
        $this->client->followRedirects();
        $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public static function providerUrls(): array
    {
        return [
            ['/']
        ];
    }
}