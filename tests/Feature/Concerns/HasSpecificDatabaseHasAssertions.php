<?php

namespace SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\ExpectationFailedException;
use SteadfastCollective\LaravelSystemLog\Models\SystemLog;

/**
 * Laravel's $this->assertDatabaseHas() method is useful, but very generic - this
 * trait adds model-specific checks which know what fields to use to find the
 * expected model without having to match everything.
 *
 * This gives you much more useful results in case of a match not being found - telling
 * you which fields didn't match (as long as the matching-fields were found).
 *
 * On success it also returns the model.
 */
trait HasSpecificDatabaseHasAssertions
{
    /**
     * An extension of assertDatabaseHas - but pass a second parameter to match a specific row
     * by known values (e.g. `user_id`) and get back a more useful error message if the row
     * exists but isn't a perfect match.
     *
     * @param  class-string<Model>  $modelClassString
     */
    public function assertDatabaseHasRow(string $modelClassString, array $data, array $matchingData, ?string $connection = null)
    {
        // Work out the table and connection from the model
        /** @var Model $model */
        $model = $modelClassString::make();
        $table = $model->getTable();
        $primaryKey = $model->getKeyName();
        $connection = $connection ?? $model->getConnectionName();

        try {
            $this->assertDatabaseHas($table, $data, $connection);
        } catch (ExpectationFailedException $e) {
            // If it's a different exception, throw it (But make a new one so the backtrace starts here)
            throw_unless(
                str_starts_with($e->getMessage(), 'Failed asserting that a row in the table ['.$table.'] matches the attributes'),
                new ExpectationFailedException($e->getMessage(), $e->getComparisonFailure())
            );

            // Get the most probable match. Also get the ID so we can tell the user which row
            // we thought was the probable match (handy for their debugging)
            $probableMatches = DB::connection($connection)->table($table)
                ->select([$primaryKey, ...array_keys($data)])
                ->where($matchingData)
                ->orderBy($primaryKey, 'desc')
                ->get();

            // If we can't be clever, throw the normal exception.
            if ($probableMatches->isEmpty()) {
                throw new ExpectationFailedException("{$e->getMessage()} (No probable match found)", $e->getComparisonFailure());
            } elseif (! $probableMatches->containsOneItem()) {
                throw new ExpectationFailedException("{$e->getMessage()} (More than one probable matches found)", $e->getComparisonFailure());
            }
            $probableMatch = (array) $probableMatches->first();

            $id = $probableMatch[$primaryKey];

            // If the user didn't pass the $primaryKey field for their comparison, remove it.
            if (! array_key_exists($primaryKey, $data)) {
                unset($probableMatch[$primaryKey]);
            }

            $this->assertSame(
                $data,
                $probableMatch,
                "The most probable match in the database (ID: {$id}) is not the same as the expected attributes."
            );

            // If we didn't do anything clever, throw the original exception
            throw new ExpectationFailedException($e->getMessage(), $e->getComparisonFailure());
        }

        return ($modelClassString)::where($data)->first();
    }

    /**
     * A basic wrapper around DatabaseHasRow, but specific for SystemLogs so we
     * can return the matching model if found.
     *
     * This is called via HasSystemLogAssertions which does some more logic and only
     * calls this if we have a model.
     */
    protected function assertDatabaseHasSystemLog(array $attributes): SystemLog
    {
        if (array_key_exists('internal_type', $attributes) && array_key_exists('internal_id', $attributes)) {
            $matchingData = [
                'internal_type' => $attributes['internal_type'],
                'internal_id' => $attributes['internal_id'],
            ];
        } elseif (array_key_exists('external_type', $attributes) && array_key_exists('external_id', $attributes)) {
            $matchingData = [
                'external_type' => $attributes['external_type'],
                'external_id' => $attributes['external_id'],
            ];
        } elseif (array_key_exists('message', $attributes)) {
            $matchingData = [
                'message' => $attributes['message'],
            ];
        } else {
            throw new \Exception('I only know how to match a SystemLog using internal details, or message');
        }
        $this->assertDatabaseHasRow(SystemLog::class, $attributes, $matchingData);

        return SystemLog::where($matchingData)->first();
    }
}
