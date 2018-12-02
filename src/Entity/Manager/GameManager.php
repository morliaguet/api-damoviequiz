<?php

namespace App\Entity\Manager;

use App\Entity\Game;
use App\Service\CustomObjectNormalizer;
use App\Service\Redis;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class GameManager
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
        $this->prefix = 'game:';
        $this->serializer = new Serializer([new CustomObjectNormalizer(null, null, null, new ReflectionExtractor())], [new JsonEncoder()]);
    }

    /**
     * @param $uuid
     * @return Game|null
     */
    public function findGame($uuid)
    {
        $game = $this->redis->get($this->prefix . $uuid);

        if (null === $game) {
            return $game;
        }

        return $this->serializer->deserialize($game, Game::class, 'json', ['disable_type_enforcement' => true]);
    }

    /**
     * @param Game $game
     */
    public function updateGame(Game $game)
    {
        $data = $this->serializer->serialize($game, 'json');

        $this->redis->set($this->prefix . $game->getUuid(), $data);
    }

    /**
     * @return Game
     */
    public function createGame()
    {
        $uuid = Uuid::uuid4();
        $game = new Game($uuid->toString());

        $this->updateGame($game);

        return $game;
    }
}
