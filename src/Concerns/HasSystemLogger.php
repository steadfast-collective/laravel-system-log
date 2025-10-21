<?php

namespace SteadfastCollective\LaravelSystemLog\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasSystemLogger
{
    /** @var \SteadfastCollective\LaravelSystemLog\Models\SystemLog */
    private $newSystemLog;

    public function addSystemLog(
        string $message,
        string $level = 'info',
        ?array $context = null,
        ?string $internalType = null,
        ?string $internalId = null,
        ?string $externalType = null,
        ?string $externalId = null,
        ?Model $model = null,
    ) {
        /** @var class-string<\SteadfastCollective\LaravelSystemLog\Models\SystemLog> $systemLogClass */
        $systemLogClass = config('system-log.class');
        $this->newSystemLog = new $systemLogClass([
            'internal_type' => $internalType,
            'internal_id' => $internalId,
            'external_type' => $externalType,
            'external_id' => $externalId,
            'log_level' => $level,
            'message' => $message,
            'context' => $context,
        ]);

        if ($model) {
            $this->inferFromClass($model);
        }

        // If this class has a method for formatting/prefixing log messages
        // then use it. Designed for use with Steadfast Collective's (currently
        // internal) HasLogger trait.
        if (is_callable([$this, 'makeLogMessage'])) {
            $message = $this->makeLogMessage($message);
        }

        Log::$level($message);

        $this->newSystemLog->save();

        // Clear the system-log-internal-type-options
        Cache::forget('system-log-internal-type-options');

        return $this->newSystemLog;
    }

    public function getInternalId(): string
    {
        return (string) $this->getKey();
    }

    public function getInternalType(): string
    {
        return get_class($this);
    }

    public function getExternalId(): ?string
    {
        // TODO: Maybe give this a default
        return null;
    }

    public function getExternalType(): string
    {
        return class_basename($this);
    }

    /**
     * As a convenience we can pass an object into addSystemLog instead of having to define
     * the internal/external types and IDs every time. This method infers those values
     * from the given object and updates the new system log.
     */
    private function inferFromClass(Model $model)
    {
        if (is_callable([$model, 'getInternalId'])) {
            $this->newSystemLog->internal_id = $model->getInternalId();
        }
        if (is_callable([$model, 'getInternalType'])) {
            $this->newSystemLog->internal_type = $model->getInternalType();
        }
        if (is_callable([$model, 'getExternalId'])) {
            $this->newSystemLog->external_id = $model->getExternalId();
        }
        if (is_callable([$model, 'getExternalType'])) {
            $this->newSystemLog->external_type = $model->getExternalType();
        }
    }
}
