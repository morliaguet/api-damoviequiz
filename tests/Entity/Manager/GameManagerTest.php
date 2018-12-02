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

use App\Entity\Game;
use App\Entity\Manager\GameManager;
use App\Entity\Question;
use App\Service\CustomObjectNormalizer;
use App\Service\Redis;
use App\Service\Tmdb;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class GameManagerTest extends TestCase
{
    private function createGameManager()
    {
        return new GameManager(new Redis());
    }

    private function getSerializer()
    {
        return new Serializer([new CustomObjectNormalizer(null, null, null, new ReflectionExtractor())], [new JsonEncoder()]);
    }

    private function getTmbdService()
    {
        return new Tmdb();
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

    public function testSerializeAndDeserializeGame()
    {
        $gameManager = $this->createGameManager();
        $serializer = $this->getSerializer();
        $tmdb = $this->getTmbdService();

        $game = $gameManager->createGame();

        list($movie, $wrongMovie) = $tmdb->getMoviesWithCredits();

        $play = (new Question())
            ->setMovie($movie['movie'])
            ->setChoices(array_merge([$movie['cast']], [$wrongMovie['cast']]))
            ->setAnswer($movie['cast']['id']);

        $game->setCurrentQuestion($play);

        $gameSerialize = $serializer->serialize($game, 'json');
        $gameDeserialize = $serializer->deserialize($gameSerialize, Game::class, 'json');

        $this->assertEquals($game, $gameDeserialize);
    }
}
