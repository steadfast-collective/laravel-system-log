<?php

namespace SteadfastCollective\LaravelSystemLog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string|null $internal_type
 * @property string|null $internal_id
 * @property string|null $external_type
 * @property string|null $external_id
 * @property string|null $log_level
 * @property string|null $message
 * @property string|null $code
 * @property array|null $context
 * @property string|null $notes
 * @property string|null $retried_at
 * @property string|null $retried_by
 * @property string|null $resolved
 *
 * @mixin \Eloquent
 */
class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal_type',
        'internal_id',
        'external_type',
        'external_id',
        'log_level',
        'code',
        'message',
        'context',
        'notes',
        'retried_at',
        'retried_by',
        'resolved',
    ];

    protected $casts = [
        'context' => 'array',
        'retried_at' => 'datetime',
        'resolved' => 'boolean',
    ];

    // TODO: Reintroduce Retrying User
    // public function retryingUser()
    // {
    //     return $this->belongsTo(User::class, 'retried_by');
    // }

    public function internalModel(): MorphTo
    {
        return $this->morphTo('internal_model', 'internal_type', 'internal_id');
    }
}
