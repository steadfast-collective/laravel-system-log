<?php

namespace App\Models;

use SteadfastCollective\LaravelSystemLog\Models\SystemLog as SteadfastSystemLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;

/**
 * @property int $id
 * @property string|null $internal_type
 * @property string|null $internal_id
 * @property string|null $external_type
 * @property string|null $external_id
 * @property string|null $log_level
 * @property string|null $message
 * @property array<array-key, mixed>|null $context
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $retried_at
 * @property string|null $retried_by
 * @property bool|null $resolved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $internalModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereExternalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereInternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereInternalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereLogLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereRetriedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereRetriedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SystemLog extends SteadfastSystemLog
{
    use Prunable;

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMonth());
    }
}
