<?php

namespace Ucscode\Pairs;

use mysqli;

abstract class AbstractPairs
{
    public const ALL = ':ALL';

    /**
     * Initializes a new instance of the Pairs class.
     * 
     * @param mysqli $mysqli An instance of the MYSQLI class for database connection.
     * @param string $table The name of the meta table.
     */
    public function __construct(protected mysqli $mysqli, protected string $table)
    {
        $this->createNonExistingTable();
    }

    /**
     * Applies a Foreign Key Constraint to the `_ref` column of the meta table. 
     * Thus, referencing the parent table.
     *
     * @param array $data - configuration data
     * @return bool 
     */
    public function setForeignConstraint(ForeignConstraint $foreignConstraint): ?bool
    {
        $placeholder = "SELECT NULL 
        FROM information_schema.TABLE_CONSTRAINTS 
        WHERE 
            CONSTRAINT_SCHEMA = DATABASE()  
            AND TABLE_NAME = '%s'
            AND CONSTRAINT_NAME = '%s' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'";

        $SQL = sprintf(
            $placeholder,
            $this->table,
            $foreignConstraint->getConstraint()
        );
    
        if($this->mysqli?->query($SQL)?->num_rows === 0) {

            $placeholder = "ALTER TABLE `%s`
            MODIFY `_ref` %s %s %s,
            ADD CONSTRAINT `%s`
            FOREIGN KEY (`_ref`)
            REFERENCES `%s`(`%s`)
            ON DELETE %s";

            $SQL = sprintf(
                $placeholder,
                $this->table,
                $foreignConstraint->getPrimaryKeyDataType(),
                $foreignConstraint->isPrimaryKeyUnsigned() ? 'UNSIGNED' : null,
                $foreignConstraint->isPrimaryKeyNullable() ? null : 'NOT NULL',
                $foreignConstraint->getConstraint(),
                $foreignConstraint->getTable(),
                $foreignConstraint->getPrimaryKeyColumn(),
                $foreignConstraint->getOnDeleteAction()
            );
            
            return $this->mysqli->query($SQL);
        }
        
        return true;
    }

    /**
     * Create meta table if it doesn't exist.
     * 
     * @return bool
     */
    protected function createNonExistingTable(): bool
    {
        $SQL = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s`(
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `_ref` INT,
                `_key` VARCHAR(255) NOT NULL,
                `_value` TEXT,
                `epoch` BIGINT NOT NULL DEFAULT (UNIX_TIMESTAMP())
            )",
            $this->table
        );
        return $this->mysqli->query($SQL);
    }
}