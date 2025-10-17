<?php

namespace SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns;

use Illuminate\Database\Eloquent\Model;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLogger;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLoggerAssertions;
use SteadfastCollective\LaravelSystemLog\Tests\TestCase;

class HasSystemLoggerTest extends TestCase
{
    use HasSystemLoggerAssertions;

    public function test_create_simple_system_log()
    {
        $model = new TestModel;
        $model->id = fake()->randomNumber();

        $model->addSystemLog('This is a test message');

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            internalType: 'SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns\TestModel',
            internalId: (string) $model->id,
            externalType: 'TestModel',
            externalId: 'my_external_id',
        );
    }

    public function test_create_system_log_with_context()
    {
        $model = new TestModel;
        $model->id = fake()->randomNumber();

        $model->addSystemLog(
            'This is a test message',
            context: [
                'some context' => [
                    'can be stored' => 'here',
                ],
            ]
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            context: [
                'some context' => [
                    'can be stored' => 'here',
                ],
            ]
        );
    }
}

class TestModel extends Model
{
    use HasSystemLogger;

    public function getExternalId(): string
    {
        return 'my_external_id';
    }
}
