<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_registers_user()
    {
        $user = User::create([
            'username' => 'noemi',
            'password' => 'segreta',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('noemi', $user->username);
        $this->assertTrue(Hash::check('segreta', $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logins_user()
    {
        $user = User::create([
            'username' => 'noemi',
            'password' => 'segreta',
        ]);

        $password= 'segreta';
        $loginSuccess = Hash::check($password, $user->password);
        $this->assertTrue($loginSuccess);

        $wrongPassword = 'topsecret';
        $loginFail = Hash::check($wrongPassword, $user->password);
        $this->assertFalse($loginFail);
    }
}
