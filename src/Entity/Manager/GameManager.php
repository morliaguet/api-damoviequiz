<?php

namespace App\Entity\Manager;

use App\Entity\Game;
use App\Service\Redis;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    /**
     * @param $uuid
     * @return Game
     */
    public function findGame($uuid)
    {
        $game = $this->redis->get($this->prefix . $uuid);

        if (null === $game) {
            throw new ResourceNotFoundException('Game does not exist');
        }

        return $this->serializer->deserialize($game, Game::class, 'json');
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
