<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\SpvClient;
use Tests\TestCase;

class SpvModuleTest extends TestCase
{
    public function test_spv_client_is_registered(): void
    {
        $this->assertTrue($this->app->bound(SpvClient::class));

        $client = $this->app->make(SpvClient::class);

        $this->assertInstanceOf(SpvClient::class, $client);
    }
}
