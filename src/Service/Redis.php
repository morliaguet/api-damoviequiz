<?php

namespace App\Service;

use Predis\Client;

class Redis
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]);
    }

    public function get($key)
    {
        if (!$this->client->exists($key)) {
            return null;
        }

        return $this->client->get($key);
    }

    /**
     * @param string $key Key to store in Redis.
     * @param string $value Value to store in Redis.
     * @return bool
     */
    public function set($key, $value)
    {
        $this->client->set($key, $value);

        return true;
    }
}
