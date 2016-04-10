<?php

namespace Siqubu\Features;

use Siqubu\Expressions\Literal;
use Siqubu\Select;

trait SetTrait
{
    /**
     * Values to set.
     *
     * @var array
     */
    protected $set_values = [];

    /**
     * Set a new value to a column.
     *
     * @param string $column Column to set
     * @param mixed $value Value applied to the column
     *
     * @return SetTrait
     */
    public function set($column, $value)
    {
        $this->set_values[$column] = $value;

        return $this;
    }

    /**
     * Renders the SET parts.
     *
     * @return string
     */
    protected function renderSet()
    {
        $fields = [];

        if (empty($this->set_values)) {
            return '';
        }

        foreach ($this->set_values as $field => $value) {
            list($field_alias, $field_value) = $this->getAliasData($field);

            // If Literal, render as is...
            if ($value instanceof Literal) {
                $value = $value->render();
            // ... if Select, render as is...
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            // ... else escape value.
            } else {
                $value = self::escape($value);
            }

            if (null !== $field_alias) {
                $field = sprintf('%s.%s', $field_alias, $field_value);
            } else {
                $field = $field_value;
            }

            $fields[] = sprintf('%s = %s', $field, $value);
        }

        return trim(sprintf('SET %s', implode(', ', $fields)));
    }
}