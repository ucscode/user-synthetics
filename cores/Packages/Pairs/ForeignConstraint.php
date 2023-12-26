<?php

namespace Ucscode\Pairs;

class ForeignConstraint
{
    protected string $constraint;
    protected string $primaryKeyColumn = 'id';
    protected string $primaryKeyDataType = 'INT';
    protected bool $primaryKeyUnsigned = false;
    protected bool $primaryKeyNullable = false;
    protected string $onDeleteAction = 'CASCADE';

    /**
     * Create a new instance of ForeignConstraint and set the foreign table name spontaneously
     */
    public function __construct(protected string $table)
    {
        $this->constraint = strtoupper('CONSTR_' . $table);
    }

    /**
     * Get the foreign table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set a custom constraint
     */
    public function setConstraint(string $constraint): self
    {
        $this->constraint = trim($constraint);
        return $this;
    }

    public function getConstraint(): string
    {
        return $this->constraint;
    }

    /**
     * Define the primary key column of the foreign table
     */
    public function setPrimaryKeyColumn(string $column): self
    {
        $this->primaryKeyColumn = trim($column);
        return $this;
    }

    /**
     * Get the primay key column of the foreign table
     */
    public function getPrimaryKeyColumn(): string
    {
        return $this->primaryKeyColumn;
    }

    /**
     * Define the primary key datatype of the foreign table
     */
    public function setPrimaryKeyDataType(string $dataType): self
    {
        $this->primaryKeyDataType = trim($dataType);
        return $this;
    }

    /**
     * Get the primary key datatype of the foreign table
     */
    public function getPrimaryKeyDataType(): string
    {
        return $this->primaryKeyDataType;
    }

    /**
     * Define the primary key signature status of the foreign table
     */
    public function setPrimaryKeyUnsigned(bool $unsigned): self
    {
        $this->primaryKeyUnsigned = $unsigned;
        return $this;
    }

    /**
     * Get the signature status of the primary key
     */
    public function isPrimaryKeyUnsigned(): bool
    {
        return $this->primaryKeyUnsigned;
    }

    /**
     * Define the primary key signature status of the foreign table
     */
    public function setPrimaryKeyNullable(bool $nullable): self
    {
        $this->primaryKeyNullable = $nullable;
        return $this;
    }

    /**
     * Get the signature status of the primary key
     */
    public function isPrimaryKeyNullable(): bool
    {
        return $this->primaryKeyNullable;
    }

    /**
     * Set the delete action for the foreign key table
     */
    public function setOnDeleteAction(string $onDeleteAction): self
    {
        $this->onDeleteAction = $onDeleteAction;
        return $this;
    }

    public function getOnDeleteAction(): string
    {
        return $this->onDeleteAction;
    }
}
