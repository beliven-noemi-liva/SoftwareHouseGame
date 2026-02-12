<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_be_created_with_username_and_password()
    {
        $user = User::factory()->create([
            'username' => 'john_doe',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'john_doe',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_has_many_games()
    {
        $user = User::factory()->create();
        $games = Game::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $user->games);
        $this->assertTrue($user->games->contains($games[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_have_no_games()
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->games);
    }
    

}
