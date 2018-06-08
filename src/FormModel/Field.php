<?php

namespace Styde\Html\FormModel;

use Styde\Html\FieldBuilder;
use Styde\Html\HandlesAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Htmlable;

class Field implements Htmlable
{
    use HasAttributes, HandlesAccess, ValidationRules;

    /**
     * @var \Styde\Html\FieldBuilder
     */
    protected $fieldBuilder;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $type;
    /**
     * @var mixed
     */
    public $value;
    /**
     * @var string
     */
    public $label;
    /**
     * @var template
     */
    public $template;
    /**
     * @var array
     */
    public $attributes = [];
    /**
     * @var array
     */
    public $extra = [];
    /**
     * @var array
     */
    protected $options = [];

    protected $table;

    protected $tableText;

    protected $tableId;

    protected $query;

    public function __construct(FieldBuilder $fieldBuilder, $name, $type = 'text')
    {
        $this->fieldBuilder = $fieldBuilder;
        $this->name = $name;
        $this->type = $type;

        $this->addRuleByFieldType($type);
        $this->nullable();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function template($template)
    {
        $this->template = $template;
        return $this;
    }

    public function extra($values, $value = true)
    {
        if (is_array($values)) {
            $this->extra = array_merge($this->extra, $values);
        } else {
            $this->extra[$values] = $value;
        }
        return $this;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function from($table, $text, $id = 'id', $query = null)
    {
        $this->table = $table;
        $this->tableText = $text;
        $this->tableId = $id;
        $this->query = $query;

        $this->setRuleExists();

        return $this;
    }

    public function getOptions()
    {
        if ($this->table) {
            $query = DB::table($this->table);

            if ($this->query) {
                call_user_func($this->query, $query);
            }

            return $query->pluck($this->tableText, $this->tableId)->all();
        }

        return $this->options;
    }

    public function getType()
    {
        return $this->type;
    }

    public function render()
    {
        if ($this->included) {
            return $this->fieldBuilder->render($this);
        }
    }

    public function toHtml()
    {
        return $this->render();
    }
}
