<?php

namespace App\Services\Anaf\Spv;

class SpvFisier
{
    public $id;
    public $continut;
    public $extensie;
    public $contentType;

    public function __construct(string $id, string $continut, string $extensie, string $contentType)
    {
        $this->id = $id;
        $this->continut = $continut;
        $this->extensie = $extensie;
        $this->contentType = $contentType;
    }

    public function numeFisier(): string
    {
        return $this->id . '.' . $this->extensie;
    }

    public function hash(): string
    {
        return hash('sha256', $this->continut);
    }
}
