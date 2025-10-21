<?php

namespace SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLogger;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLoggerAssertions;
use SteadfastCollective\LaravelSystemLog\Tests\TestCase;

class HasSystemLoggerTest extends TestCase
{
    use HasSystemLogger;
    use HasSystemLoggerAssertions;
    use HasSpecificDatabaseHasAssertions;

    public function test_create_simple_system_log_no_context()
    {
        $model = new TestModel;
        $model->id = 12345;

        $this->addSystemLog(
            message: 'This is a test message',
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            internalType: 'SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns\TestModel',
            internalId: '12345',
            externalType: 'TestModel',
            externalId: 'my_external_id',
        );
    }

    public function test_create_system_log_with_context()
    {
        $model = new TestModel;
        $model->id = fake()->randomNumber();

        $this->addSystemLog(
            'This is a test message',
            model: $model,
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

    public function test_infer_from_model_uses_null_for_empty_strings()
    {

        $model = new TestModel;
        $model->my_external_id = '';

        $this->addSystemLog(
            'This is a test message',
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            externalId: null,
            externalType: null,
        );
    }

    public function test_infer_does_nothing_if_inferring_methods_do_not_exist()
    {
        $model = new TestModelWithoutHasSystemLogger();

        $this->addSystemLog(
            'This is a test message',
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            internalId: null,
            internalType: null,
            externalId: null,
            externalType: null,
        );
    }

    /**
     * If the given model has a makeLogMessage method, use that to
     * format the message for the logger (but not for the SystemLog)
     */
    public function test_make_log_message_is_used_for_logging()
    {
        $model = new TestModelWithMakeLogMessage();
        $model->id = 1;

        Log::expects('info')
            ->with('[TestModelWithMakeLogMessage#1] This is a test message');

        $this->addSystemLog(
            'This is a test message',
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            internalId: null,
            internalType: null,
            externalId: null,
            externalType: null,
        );
    }
}
/**
 * @property int|null $id
 *
 * @mixin \Eloquent
 */
class TestModel extends Model
{
    use HasSystemLogger;

    public function getExternalId(): string
    {
        return 'my_external_id';
    }
}

/**
 * @property int|null $id
 *
 * @mixin \Eloquent
 */
class TestModelWithoutHasSystemLogger extends Model
{

}

/**
 * @property int|null $id
 *
 * @mixin \Eloquent
 */
class TestModelWithMakeLogMessage extends Model
{
    function makeLogMessage(string $message): string
    {
        return sprintf(
            '[%s#%s] %s',
            class_basename($this),
            $this->id,
            $message
        );
    }
}
