<?php

namespace App\Service;

use Tmdb\ApiToken;
use Tmdb\Client;

class Tmdb
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $apiToken = new ApiToken(getenv('TMDB_API_TOKEN'));
        $this->client = new Client($apiToken);
    }

    /**
     * @return array
     */
    public function getRandomMovies()
    {
        $movies = $this->client->getDiscoverApi()->discoverMovies();

        return $movies['results'];
    }

    /**
     * @param int $movieId
     * @return array
     */
    public function getCredits(int $movieId)
    {
        $credits = $this->client->getMoviesApi()->getCredits($movieId);

        return $credits['cast'];
    }

    public function getMoviesWithCredits(int $limit = 2) {
        $movies = $this->getRandomMovies();

        $list = [];
        for($i = 1; $i <= $limit; $i++) {
            $randomMovie = rand(0, count($movies)-1);
            $casting = $this->getCredits($movies[$randomMovie]['id']);

            $randomCast = rand(0, count($casting)-1);
            $list[] = [
                'movie' => $movies[$randomMovie]['title'],
                'cast' => [
                    'id' => $casting[$randomCast]['id'],
                    'name' => $casting[$randomCast]['name']
                ]
            ];
        }

        return $list;
    }
}
