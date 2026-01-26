<?php

namespace SteadfastCollective\LaravelSystemLog\Tests\Feature\Concerns;

use Illuminate\Database\Eloquent\Model;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLogger;
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLoggerAssertions;
use SteadfastCollective\LaravelSystemLog\Tests\TestCase;

class AssertContextNormalisationTest extends TestCase
{
    use HasSpecificDatabaseHasAssertions;
    use HasSystemLogger;
    use HasSystemLoggerAssertions;

    /**
     * Test that Arr::sortRecursive normalizes array key order at all nesting levels
     * Verifies:
     * - Root-level key sorting
     * - Nested array key sorting
     * - Deeply nested array key sorting
     * - Mixed associative and indexed arrays
     * - Various data types (string, number, float, bool, null)
     */
    public function test_context_assertion_normalizes_array_key_order_recursively()
    {
        $model = new TestModelForAssertions;
        $model->id = 1;

        // Log with keys in specific order at all levels
        $this->addSystemLog(
            'Test message',
            model: $model,
            context: [
                'z_root' => 'value_z',
                'a_root' => 'value_a',
                'nested' => [
                    'z_nested' => 'nested_z',
                    'a_nested' => 'nested_a',
                    'deep' => [
                        'z_deep' => 'deep_z',
                        'a_deep' => 'deep_a',
                    ],
                ],
                'mixed' => [
                    'associative_z' => 'assoc_z',
                    'associative_a' => 'assoc_a',
                    'indexed' => ['item1', 'item2', 'item3'],
                ],
            ]
        );

        // Assert with completely different key order at all levels
        // This should pass because Arr::sortRecursive normalizes all levels
        $this->assertSystemLogLogged(
            message: 'Test message',
            context: [
                'a_root' => 'value_a',
                'nested' => [
                    'a_nested' => 'nested_a',
                    'deep' => [
                        'a_deep' => 'deep_a',
                        'z_deep' => 'deep_z',
                    ],
                    'z_nested' => 'nested_z',
                ],
                'mixed' => [
                    'associative_a' => 'assoc_a',
                    'associative_z' => 'assoc_z',
                    'indexed' => ['item1', 'item2', 'item3'],
                ],
                'z_root' => 'value_z',
            ]
        );
    }

    /**
     * Test that assertEquals ensures exact value comparison after normalization
     * (Not using assertEqualsCanonicalizing which would be more lenient)
     * Also verifies numeric string keys are handled correctly
     */
    public function test_context_assertion_uses_exact_comparison_with_various_types()
    {
        $model = new TestModelForAssertions;
        $model->id = 2;

        $this->addSystemLog(
            'Test message',
            model: $model,
            context: [
                '3' => 'value3',
                'string' => 'value',
                'bool' => true,
                'null' => null,
                '1' => 'value1',
                'number' => 123,
                'float' => 45.67,
                '2' => 'value2',
            ]
        );

        // Assert with different key order but exact same values and types
        $this->assertSystemLogLogged(
            message: 'Test message',
            context: [
                '1' => 'value1',
                '2' => 'value2',
                '3' => 'value3',
                'bool' => true,
                'float' => 45.67,
                'null' => null,
                'number' => 123,
                'string' => 'value',
            ]
        );
    }

    /**
     * Test that context assertion fails when values don't match
     * (Verifies that normalization still requires exact value equality)
     */
    public function test_context_assertion_fails_when_values_differ()
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);

        $model = new TestModelForAssertions;
        $model->id = 3;

        $this->addSystemLog(
            'Test message',
            model: $model,
            context: [
                'key' => 'expected_value',
            ]
        );

        // Try to assert with different value
        $this->assertSystemLogLogged(
            message: 'Test message',
            context: [
                'key' => 'different_value',
            ]
        );
    }
}

/**
 * @property int|null $id
 * @property string|null $my_external_id
 *
 * @mixin \Eloquent
 */
class TestModelForAssertions extends Model
{
    use HasSystemLogger;

    public function getExternalId(): string
    {
        return 'my_external_id';
    }
}
