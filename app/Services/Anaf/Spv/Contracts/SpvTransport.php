<?php

namespace App\Services\Anaf\Spv\Contracts;

use Illuminate\Http\Client\Response;

interface SpvTransport
{
    public function get($path, array $query = array()): Response;
}
