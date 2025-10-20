<?php

namespace SteadfastCollective\LaravelSystemLog\Tables\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

/**
 * A range filter for Filament Tables. Adds two fields for the given field - if only one is filled in, then an exact
 * match filter is performed, but if both are filled in then a range filter is performed.
 */
class RangeFilter extends Filter
{
    /**
     * After we generate all our names/strings - store them here because it's easier
     * than passing them into the closures below individually.
     */
    private stdClass $rangeFilterNames;

    /**
     * Pass in the class name for the form component you would like to use. Valid options include:
     * - Filament\Forms\Components\TextInput::class
     * - Filament\Forms\Components\DatePicker::class
     */
    public function using(string $format, ?string $alternateName = null): self
    {
        $name = $this->getName();

        $this->rangeFilterNames = new stdClass;
        $this->rangeFilterNames->field = $name;
        $this->rangeFilterNames->fromField = $name.'_from';
        $this->rangeFilterNames->toField = $name.'_to';
        $this->rangeFilterNames->label = $alternateName ?? $this->getLabel();

        // We can create range filters which use a Text input, or a range inpout.
        switch ($format) {
            case TextInput::class:
                $this->form([
                    TextInput::make($this->rangeFilterNames->fromField)->label($this->rangeFilterNames->label.': From'),
                    TextInput::make($this->rangeFilterNames->toField)->label($this->rangeFilterNames->label.': To'),
                ]);
                break;
            case DatePicker::class:
                $this->form([
                    DatePicker::make($this->rangeFilterNames->fromField)->label($this->rangeFilterNames->label.': From')->native(false),
                    DatePicker::make($this->rangeFilterNames->toField)->label($this->rangeFilterNames->label.': To')->native(false),
                ]);
                break;
            default:
                throw new \Exception("Unsupported RangeFilter format: {$format}");
        }

        // Setup the Query filter
        $this->query(function (Builder $query, array $data): Builder {
            return $query
                ->when(
                    $data[$this->rangeFilterNames->fromField],
                    function (Builder $query, $value) use ($data): Builder {
                        if (empty($data[$this->rangeFilterNames->toField])) {
                            return $query->where($this->rangeFilterNames->field, '=', $value);
                        }

                        return $query->where($this->rangeFilterNames->field, '>=', $value);
                    }
                )
                ->when(
                    $data[$this->rangeFilterNames->toField],
                    fn (Builder $query, $value): Builder => $query->where($this->rangeFilterNames->field, '<=', $value)
                );
        });

        return $this;
    }
}
