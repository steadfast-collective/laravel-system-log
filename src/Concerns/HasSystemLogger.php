<?php

namespace SteadfastCollective\LaravelSystemLog\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SteadfastCollective\LaravelSystemLog\Models\SystemLog;

trait HasSystemLogger
{
    private SystemLog $newSystemLog;

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
        $this->newSystemLog = new SystemLog([
            'internal_type' => $internalType ?? $this->getInternalType(),
            'internal_id' => $internalId ?? $this->getInternalId(),
            'external_type' => $externalType ?? $this->getExternalType(),
            'external_id' => $externalId ?? $this->getExternalId(),
            'log_level' => $level,
            'message' => $message,
            'context' => $context,
        ]);

        if ($model) {
            $this->inferFromClass($model);
        }

        Log::$level('[SystemLog] '.$message);

        $this->newSystemLog->save();
        ray($this->newSystemLog);

        // Clear the system-log-internal-type-options
        Cache::forget('system-log-internal-type-options');

        return $this->newSystemLog;
    }

    public function getInternalId(): string
    {
        ray('here', $this->getKey());

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
     * As a convenience we can pass a model into addSystemLog instead of having to define
     * the internal/external types and IDs every time. This method infers those values
     * from the given model and returns an array we can merge before creting the Log.
     */
    private function inferFromClass(Model $model)
    {
        $this->newSystemLog->internal_id = $model->getInternalId();
        $this->newSystemLog->internal_type = $model->getInternalType();
        $this->newSystemLog->external_id = $model->getExternalId();
        $this->newSystemLog->external_type = $model->getExternalType();
    }
}
