<?php
/*
 * This file is part of the UPro package.
 *
 * Copyright (c) U Pro <dev@u-pro.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Entity\Manager;

use App\Entity\Manager\GameManager;
use App\Service\Redis;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GameManagerTest extends TestCase
{
    private function createGameManager()
    {
        return new GameManager(new Redis());
    }

    private function getSerializer()
    {
        return new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function testFindGame()
    {
        $gameManager = $this->createGameManager();
        $serializer = $this->getSerializer();

        $game = $gameManager->createGame();
        $gameFinded = $gameManager->findGame($game->getUuid());

        $gameSerialized = $serializer->serialize($game, 'json');
        $gameFindedSerialized = $serializer->serialize($gameFinded, 'json');

        $this->assertEquals($gameSerialized, $gameFindedSerialized);
    }

    public function testUpdateGame()
    {
        $gameManager = $this->createGameManager();
        $serializer = $this->getSerializer();

        $game = $gameManager->createGame();
        $game->increaseScore();
        $game->setCompleted(true);

        $gameManager->updateGame($game);

        $gameFinded = $gameManager->findGame($game->getUuid());

        $gameSerialized = $serializer->serialize($game, 'json');
        $gameFindedSerialized = $serializer->serialize($gameFinded, 'json');

        $this->assertEquals($gameSerialized, $gameFindedSerialized);
    }
}
