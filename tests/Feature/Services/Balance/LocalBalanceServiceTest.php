<?php

namespace Tests\Feature\Services\Balance;

use App\Models\User;
use App\Services\Balance\LocalBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class LocalBalanceServiceTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;

    /** @test */
    public function it_returns_current_user_balance()
    {
        $this->actingAs(User::factory()->createOne(['balance' => 100]));

        $service = app(LocalBalanceService::class);
        $result = $service->getCurrentUserBalance();

        $this->assertEquals(100, $result);
    }

    /** @test */
    public function it_returns_user_balance()
    {
        $user = User::factory()->createOne(['balance' => 100]);

        $service = app(LocalBalanceService::class);
        $result = $service->getUserBalance($user->id);

        $this->assertEquals(100, $result);
    }

    /** @test */
    public function it_increments_user_balance()
    {
        $user = User::factory()->createOne(['balance' => 100]);

        $service = app(LocalBalanceService::class);
        $service->incrementUserBalance($user->id, 100);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 200,
        ]);
    }

    /** @test */
    public function it_decrements_user_balance()
    {
        $user = User::factory()->createOne(['balance' => 100]);

        $service = app(LocalBalanceService::class);
        $service->decrementUserBalance($user->id, 100);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 0,
        ]);
    }
}
