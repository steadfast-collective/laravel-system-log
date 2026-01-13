<?php

namespace SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLogger;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLoggerAssertions;
use SteadfastCollective\LaravelSystemLog\Tests\TestCase;

class HasSystemLoggerTest extends TestCase
{
    use HasSpecificDatabaseHasAssertions;
    use HasSystemLogger;
    use HasSystemLoggerAssertions;

    public function test_create_simple_system_log_no_context()
    {
        $model = new TestModel;
        $model->id = 12345;

        $this->addSystemLog(
            message: 'This is a test message',
            code: 'SOME_CODE',
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            code: 'SOME_CODE',
            internalType: 'SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns\TestModel',
            internalId: '12345',
            externalType: 'TestModel',
            externalId: 'my_external_id',
        );
    }

    public function test_create_system_log_accepts_enums()
    {
        $model = new TestModel;
        $model->id = 12345;

        $this->addSystemLog(
            message: 'This is a test message',
            code: TestSystemLogCodes::API_RATE_LIMIT_EXCEEDED,
            model: $model,
        );

        $this->assertSystemLogLogged(
            message: 'This is a test message',
            code: 'API-1001',
            internalType: 'SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns\TestModel',
            internalId: '12345',
            externalType: 'TestModel',
            externalId: 'my_external_id',
        );

        // Also check assertSystemLogLogged accepts an enum
        $this->assertSystemLogLogged(
            message: 'This is a test message',
            code: TestSystemLogCodes::API_RATE_LIMIT_EXCEEDED,
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
        $model = new TestModelWithoutHasSystemLogger;

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
        $model = new TestModelWithMakeLogMessage;
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
 * @property string|null $my_external_id
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
class TestModelWithoutHasSystemLogger extends Model {}

/**
 * @property int|null $id
 *
 * @mixin \Eloquent
 */
class TestModelWithMakeLogMessage extends Model
{
    public function makeLogMessage(string $message): string
    {
        return sprintf(
            '[%s#%s] %s',
            class_basename($this),
            $this->id,
            $message
        );
    }
}

/**
 * Example SystemLog codes with some example use cases:
 *
 * 1. API_RATE_LIMIT_EXCEEDED - External service rate limiting
 * 2. DATABASE_CONNECTION_FAILED - Infrastructure failures
 * 3. PAYMENT_PROCESSING_ERROR - Transaction/financial errors
 * 4. INVALID_DATA_FORMAT - Data validation issues
 * 5. AUTHENTICATION_FAILED - Security-related events
 *
 * These codes allow:
 * - Quick identification of error types across logs
 * - Programmatic filtering and alerting (e.g., alert on all PAYMENT_* codes)
 * - Statistical analysis of error frequency by type
 * - Standardized error reporting across the application
 */
enum TestSystemLogCodes: string
{
    // External API Integration Errors (1000-1999)
    case API_RATE_LIMIT_EXCEEDED = 'API-1001';
    case API_INVALID_CREDENTIALS = 'API-1002';
    case API_SERVICE_UNAVAILABLE = 'API-1003';

    // Database & Infrastructure Errors (2000-2999)
    case DATABASE_CONNECTION_FAILED = 'DB-2001';
    case DATABASE_QUERY_TIMEOUT = 'DB-2002';
    case CACHE_SERVICE_DOWN = 'CACHE-2003';

    // Payment & Transaction Errors (3000-3999)
    case PAYMENT_PROCESSING_ERROR = 'PAY-3001';
    case PAYMENT_DECLINED = 'PAY-3002';
    case REFUND_FAILED = 'PAY-3003';

    // Data Validation Errors (4000-4999)
    case INVALID_DATA_FORMAT = 'VAL-4001';
    case MISSING_REQUIRED_FIELD = 'VAL-4002';
    case DATA_CONSTRAINT_VIOLATION = 'VAL-4003';

    // Security & Authentication Errors (5000-5999)
    case AUTHENTICATION_FAILED = 'AUTH-5001';
    case UNAUTHORIZED_ACCESS = 'AUTH-5002';
    case TOKEN_EXPIRED = 'AUTH-5003';
}
