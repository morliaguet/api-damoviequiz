# Api DataMovieQuiz

## Requis

* PHP 7.2.*
* Redis 4.0*.
* API Key The movie dataBase

## Installation

* composer install
* cp .env.dist .env

## Endpoints

* POST /game : crée une partie et retourne l'uuid
* GET /game/{uuid} : retourne les informations de la partie
* GET /game/{uuid}/play : retourne la question en cours si la partie n'est pas terminée, ou en génère une nouvelle si pas de question associée
* POST /game/{uuid}/play : vérifie la réponse envoyée en paramètre POST { answer : cast.id } et incrémente et réinitialise la question ou clôture la partie

Cast.id : id de l'acteur
