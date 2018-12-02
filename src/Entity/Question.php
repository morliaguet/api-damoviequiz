<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Question
{
    /**
     * @var string
     */
    private $movie;

    /**
     * @var array
     */
    private $choices;

    /**
     * @var int
     */
    private $answer;

    public function __construct()
    {
        $this->choices = [];
    }

    /**
     * @return string
     */
    public function getMovie(): string
    {
        return $this->movie;
    }

    /**
     * @param string $movie
     * @return Question
     */
    public function setMovie(string $movie): Question
    {
        $this->movie = $movie;
        return $this;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     * @return Question
     */
    public function setChoices(array $choices): Question
    {
        $this->choices = $choices;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnswer(): int
    {
        return $this->answer;
    }

    /**
     * @param int $answer
     * @return Question
     */
    public function setAnswer(int $answer): Question
    {
        $this->answer = $answer;
        return $this;
    }
}
