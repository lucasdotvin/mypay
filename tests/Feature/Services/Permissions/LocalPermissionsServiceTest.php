<?php

namespace Tests\Feature\Services\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Permissions\LocalPermissionsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class LocalPermissionsServiceTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;

    /** @test */
    public function it_returns_true_if_the_user_has_the_expected_permission()
    {
        $role = Role::factory()
            ->hasAttached($permission = Permission::factory()->createOne())
            ->createOne();

        $user = User::factory()
            ->for($role)
            ->createOne();

        $service = new LocalPermissionsService;
        $result = $service->userCan($user->id, $permission->slug);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_if_the_user_does_not_have_the_expected_permission()
    {
        $role = Role::factory()
            ->hasAttached($permission = Permission::factory()->createOne())
            ->createOne();

        $user = User::factory()
            ->for($role)
            ->createOne();

        $service = new LocalPermissionsService;
        $result = $service->userCan($user->id, 'some-permission');

        $this->assertFalse($result);
    }
}
