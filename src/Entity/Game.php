<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Game
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $score;

    /**
     * @var bool
     */
    private $completed;

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
        $this->score = 0;
        $this->completed = false;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function increaseScore(?int $number = 1): self
    {
        $this->score += $number;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(?bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }
}
