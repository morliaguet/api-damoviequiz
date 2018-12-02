<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Manager\GameManager;
use App\Entity\Question;
use App\Service\Tmdb;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameController extends FOSRestController
{
    /**
     * @var GameManager
     */
    private $gameManager;

    /**
     * @var Tmdb
     */
    private $tmdb;

    public function __construct(GameManager $gameManager, Tmdb $tmdb)
    {
        $this->gameManager = $gameManager;
        $this->tmdb = $tmdb;
    }

    /**
     * Retrieves details for a single game.
     * @Rest\Get("/game/{uuid}")
     * @Rest\View
     *
     * @param Game $game the game uuid
     *
     * @return $game
     *
     */
    public function getAction($uuid)
    {
        $game = $this->gameManager->findGame($uuid);

        if (!$game) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        return $game;
    }

    /**
     * create a game
     * @Rest\Post("/game")
     * @Rest\View
     *
     * @return View Returns the created game when successful.
     */
    public function postAction()
    {
        $game = $this->gameManager->createGame();

        return View::create($game, Response::HTTP_CREATED);
    }

    /**
     * Generate new question
     * @Rest\Get("/game/{uuid}/play")
     * @Rest\View
     *
     * @param Game $game the game uuid
     *
     * @return $game
     *
     */
    public function getPlayAction($uuid)
    {
        $game = $this->gameManager->findGame($uuid);

        if (!$game) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        if ($game->isCompleted()) {
            return View::create(null, Response::HTTP_FORBIDDEN);
        }

        if (!$game->getCurrentQuestion()) {
            list($movie, $wrongMovie) = $this->tmdb->getMoviesWithCredits();

            $play = (new Question())
                ->setMovie($movie['movie'])
                ->setChoices(array_merge([$movie['cast']], [$wrongMovie['cast']]))
                ->setAnswer($movie['cast']['id']);

            $game->setCurrentQuestion($play);
            $this->gameManager->updateGame($game);
        }

        return View::create($game->getCurrentQuestion(), Response::HTTP_OK);
    }

    /**
     *
     * @Rest\Post("/game/{uuid}/play")
     * @Rest\RequestParam(name="answer", requirements="\d+")
     * @Rest\View
     *
     * @param Game $game the game uuid
     *
     * @return $game
     *
     */
    public function postPlayAction(ParamFetcherInterface $paramFetcher, $uuid)
    {
        $game = $this->gameManager->findGame($uuid);

        if (!$game) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        $answer = (int) $paramFetcher->get('answer', null);

        $currentQuestion = $game->getCurrentQuestion();
        if ($currentQuestion && $currentQuestion->getAnswer() === $answer) {
            $game
                ->increaseScore()
                ->setCurrentQuestion(null);
        } else {
            $game->setCompleted(true);
        }

        $this->gameManager->updateGame($game);

        return View::create($game, Response::HTTP_OK);
    }
}
