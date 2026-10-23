<?php

namespace Tests\Unit;

use App\Models\User;
use InvalidArgumentException;
use Tests\TestCase;

class UserRoleSeparationTest extends TestCase
{
    public function test_it_rejects_new_student_users(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::create(['role' => 'eleve']);
    }

    public function test_it_rejects_new_teacher_users(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::create(['role' => 'enseignant']);
    }
}
