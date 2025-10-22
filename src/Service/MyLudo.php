<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MyLudo
{
    private const CACHE_TTL = 86400;
    private const USER_ID = 38350;
    private const PROFILE_URL_FORMAT = 'https://www.myludo.fr/#!/profil/jouons-ensemble-%d';
    private const LATEST_URL_FORMAT = 'https://www.myludo.fr/views/profil/datas.php?type=collection&id=%d&order=bydatedesc';

    private ?string $extractedCsrfToken = null;
    private ?string $extractedSessionCookie = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getLatestArrivals(int $limit = 12): array
    {
        $limit = max(1, $limit);
        $cacheKey = 'myludo.latest_arrivals';

        $games = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $this->getProfileContent();

            return $this->fetchLatestGames();
        });

        if (!is_array($games) || $games === []) {
            $this->cache->delete($cacheKey);

            return [];
        }

        return array_slice($games, 0, $limit);
    }

    private function getProfileContent(): void
    {
        try {
            $response = $this->httpClient->request('GET', sprintf(self::PROFILE_URL_FORMAT, self::USER_ID));
            $content = $response->getContent();

            // Extract CSRF token from HTML
            if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $content, $matches)) {
                $this->extractedCsrfToken = $matches[1];
            }

            // Extract session cookie from response headers
            $headers = $response->getHeaders();
            if (isset($headers['set-cookie'])) {
                foreach ($headers['set-cookie'] as $cookie) {
                    if (str_starts_with($cookie, 'MYLUDO_SESSID=')) {
                        $cookieParts = explode('=', $cookie, 2);
                        if (isset($cookieParts[1])) {
                            $sessionValue = explode(';', $cookieParts[1])[0];
                            $this->extractedSessionCookie = 'MYLUDO_SESSID=' . $sessionValue;
                        }
                        break;
                    }
                }
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to fetch MyLudo profile page.', [
                'exception' => $exception,
            ]);
        }
    }

    private function fetchLatestGames(): array
    {
        try {
            $response = $this->httpClient->request('GET', sprintf(self::LATEST_URL_FORMAT, self::USER_ID), [
                'headers' => $this->getHeaders(),
            ]);

            $payload = $response->toArray(false);
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to fetch latest games from MyLudo API.', [
                'exception' => $exception,
            ]);

            return [];
        }

        if (!isset($payload['list']) || !is_array($payload['list'])) {
            return [];
        }

        $games = [];
        foreach ($payload['list'] as $rawGame) {
            if (!is_array($rawGame)) {
                continue;
            }

            $id = $rawGame['id'] ?? null;
            $code = $rawGame['code'] ?? null;
            $title = $rawGame['title'] ?? null;
            $images = $rawGame['image'] ?? [];

            if (!$id || !$code || !$title || !is_array($images)) {
                continue;
            }

            $imageUrl = $images['S160'] ?? $images['S80'] ?? $images['S300'] ?? null;
            if (!$imageUrl || !is_string($imageUrl)) {
                continue;
            }

            $games[] = [
                'id' => (string) $id,
                'title' => (string) $title,
                'slug' => (string) $code,
                'url' => sprintf('https://www.myludo.fr/#!/game/%s-%s', $code, $id),
                'image' => $imageUrl,
            ];
        }

        return $games;
    }

    private function getHeaders(): array
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:144.0) Gecko/20100101 Firefox/144.0',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
            'Referer' => 'https://www.myludo.fr/',
            'X-Requested-With' => 'XMLHttpRequest',
            'Sec-GPC' => '1',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'Connection' => 'keep-alive',
            'Alt-Used' => 'www.myludo.fr',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
            'TE' => 'trailers'
        ];
        
        $headers['X-Csrf-Token'] = $this->extractedCsrfToken ?? '';
        $headers['Cookie'] = $this->extractedSessionCookie ?? '';

        return $headers;
    }
}
