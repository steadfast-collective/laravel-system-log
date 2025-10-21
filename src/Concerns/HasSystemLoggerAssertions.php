<?php

namespace SteadfastCollective\LaravelSystemLog\Concerns;

use Illuminate\Database\Eloquent\Model;
use SteadfastCollective\LaravelSystemLog\Models\SystemLog;

trait HasSystemLoggerAssertions
{
    public function assertSystemLogLogged(
        ?string $message = null,
        ?string $level = null,
        ?array $context = null,
        string|int|null $internalType = null,
        string|int|null $internalId = null,
        ?string $externalType = null,
        ?string $externalId = null,
        ?Model $model = null,
    ): SystemLog {
        $expected = [];

        $where = [];

        if ($message) {
            $expected['message'] = $message;
            $where['message'] = $message;
        }

        if ($model) {
            $expected = array_merge($expected, $this->inferFromModelAssertionVersion($model));
            $where['internal_type'] = $model::class;
            $where['internal_id'] = $model->getKey();
        }

        if ($internalType || $internalId) {
            throw_if(empty($internalType) || empty($internalId), 'Please specify both internalType and internalID as a pair');
            $expected['internal_type'] = (string) $internalType;
            $where['internal_type'] = (string) $internalType;

            $expected['internal_id'] = (string) $internalId;
            $where['internal_id'] = (string) $internalId;
        }

        if ($externalType || $externalId) {
            throw_if(empty($externalType) || empty($externalId), 'Please specify both externalType and externalID as a pair');
            $expected['external_type'] = $externalType;
            $where['external_type'] = $externalType;

            $expected['external_id'] = $externalId;
            $where['external_id'] = $externalId;
        }

        if ($level) {
            $where['log_level'] = $level;
        }

        throw_if(empty($where), new \Exception('No fields to search for in System Log Assertions'));

        // Look for the log we expect - but without the context just yet.
        $expectedSystemLog = SystemLog::where($where)->first();

        // If we didn't find anything and if we can call the assertDatabaseHasSystemLog method, call it
        // because it gives nicer output. If we did find something, this is skipped and we check the context.
        if (! is_a($expectedSystemLog, SystemLog::class)) {
            if (is_callable([$this, 'assertDatabaseHasSystemLog'])) {
                $this->assertDatabaseHasSystemLog($expected);
                $this->fail('assertDatabaseHasSystemLog did not fail, which means a mixup in expected logic between the two traits');
                /** @phpstan-ignore-next-line */
            } else {
                $this->fail('SystemLog not found. Add HasSpecificDatabaseHasAssertions to your test class for more output');
            }
        }

        // Compare the context
        if ($context) {
            $actualContext = $expectedSystemLog->context;

            // Sort the fields so the comparison is order-insensitive
            if (is_string(array_keys($context)[0])) {
                ksort($context);
                ksort($actualContext);
            } else {
                sort($context);
                sort($actualContext);
            }

            $this->assertEqualsCanonicalizing(
                $context,
                $actualContext,
                'The context was not right',
            );
        }
        // If we got here without failing, all is good!
        $this->addToAssertionCount(1);

        return SystemLog::where($expected)->first();
    }

    public function assertSystemLogNotLogged(string $message, ?Model $model = null, ?array $context = null)
    {
        $data = [
            'message' => $message,
        ];

        if ($model) {
            $data = array_merge($data, $this->inferFromModelAssertionVersion($model));
        }

        if ($context) {
            $data['context'] = $context;
        }

        $this->assertDatabaseMissing('system_logs', $data);
    }

    /**
     * Pretty much a clone of HasSystemLogger::inferFromModelAssertionVersion - duplicated
     * because this is a test trait and we want to... test what it's doing
     * and not just use it.
     *
     * The name difference is so a test can use both HasSystemLog and HasSystemLogAssertions
     */
    private function inferFromModelAssertionVersion(Model $model)
    {
        $return = [];
        if (is_callable([$model, 'getInternalId'])) {
            $return['internal_id'] = $model->getInternalId();
        }
        if (is_callable([$model, 'getInternalType'])) {
            $return['internal_type'] = $model->getInternalType();
        }
        if (is_callable([$model, 'getExternalId'])) {
            $return['external_id'] = $model->getExternalId();
        }
        if (is_callable([$model, 'getExternalType'])) {
            $return['external_type'] = $model->getExternalType();
        }

        return $return;
    }
}
