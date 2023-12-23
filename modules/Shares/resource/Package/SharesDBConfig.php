<?php

namespace Shares\Package;

use Uss;

class SharesDBConfig
{
    public function __construct()
    {
        $tableset = [
            $this->gatewayDB(),
            $this->sharesGroupDB(),
            $this->sharesDB(),
            $this->investmentDB(),
            $this->depositDB(),
            $this->withdrawalDB(),
        ];

        $uss = Uss::instance();

        try {
            foreach($tableset as $SQL) {
                $result = $uss->mysqli->query($SQL);
            }
        } catch(\Exception $e) {
            die(
                "<pre>" . implode("\n<br>\n", [
                    "SHARES: Database Configuration Error",
                    $SQL,
                    $e->getMessage(),
                ]) . "</pre>"
            );
        }

        return $this;
    }

    protected function table(string $name): string
    {
        return DB_PREFIX . $name;
    }

    protected function gatewayDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('gateway')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            userid INT UNSIGNED, /* CREATED BY */
            FOREIGN KEY (userid) REFERENCES {$this->table('users')}(id) ON DELETE SET NULL,
            method VARCHAR(255) NOT NULL,
            icon VARCHAR(255),
            detail JSON,
            model VARCHAR(20) NOT NULL, /* DEPOSIT | WITHDRWAWAL */
            `status` TINYINT NOT NULL DEFAULT 0
        )";
    }

    protected function sharesGroupDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('shares_group')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            group_name VARCHAR(255) NOT NULL
        )";
    }

    protected function sharesDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('shares')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            uuid VARCHAR(100) NOT NULL UNIQUE,
            userid INT UNSIGNED, /* CREATED BY */
            FOREIGN KEY (userid) REFERENCES {$this->table('users')}(id) ON DELETE SET NULL,
            title VARCHAR(255) NOT NULL,
            min_amount FLOAT NOT NULL DEFAULT 0.00,
            group_id INT,
            FOREIGN KEY (group_id) REFERENCES {$this->table('shares_group')}(id) ON DELETE SET NULL,
            subgroup_id INT,
            FOREIGN KEY (subgroup_id) REFERENCES {$this->table('shares_group')}(id) ON DELETE SET NULL,
            credit_bonus FLOAT NOT NULL DEFAULT 0,
            daily_increment FLOAT NOT NULL DEFAULT 1 /* IN PERCENTAGE */,
            end_after_day INT NOT NULL DEFAULT 0, /* 0 = UNLIMITED */
            `date` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP()),
            `status` VARCHAR(20) NOT NULL DEFAULT 'inactive'
        )";
    }

    protected function investmentDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('investments')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            uniqid VARCHAR(100) NOT NULL UNIQUE,
            shares_uuid VARCHAR(100),
            FOREIGN KEY (shares_uuid) REFERENCES {$this->table('shares')}(uuid) ON DELETE SET NULL,
            userid INT UNSIGNED NOT NULL,
            FOREIGN KEY (userid) REFERENCES {$this->table('users')}(id) ON DELETE CASCADE,
            title VARCHAR(255) NOT NULL,
            shares_amount FLOAT NOT NULL,
            profit_amount FLOAT NOT NULL DEFAULT 0,
            group_id INT NOT NULL,
            subgroup_id INT NOT NULL,
            credit_bonus FLOAT NOT NULL DEFAULT 0,
            daily_increment FLOAT NOT NULL,
            total_increment FLOAT NOT NULL DEFAULT 0,
            end_after_day INT NOT NULL DEFAULT 0,
            days_elapsed INT NOT NULL DEFAULT 0,
            activation_date TIMESTAMP,
            last_increment_date TIMESTAMP,
            `date` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP()),
            `status` VARCHAR(20) NOT NULL DEFAULT 'static' /* INACTIVE | ACTIVE | EXPIRED */
        )";
    }

    protected function depositDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('deposit')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            uniqid VARCHAR(255) NOT NULL UNIQUE,
            method VARCHAR(255) NOT NULL,
            amount FLOAT NOT NULL,
            userid INT UNSIGNED, /* DEPOSITED BY */
            FOREIGN KEY (userid) REFERENCES {$this->table('users')}(id) ON DELETE SET NULL,
            tx_ref VARCHAR(100) NOT NULL UNIQUE,
            screenshot VARCHAR(255),
            detail_snapshot JSON,
            funded TINYINT NOT NULL DEFAULT 0,
            `date` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP()),
            `status` VARCHAR(100) NOT NULL DEFAULT 'pending'
        )";
    }

    protected function withdrawalDB(): string
    {
        return "CREATE TABLE IF NOT EXISTS {$this->table('withdrawal')}(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            uniqid VARCHAR(255) NOT NULL UNIQUE,
            userid INT UNSIGNED NOT NULL, /* REQUESTED BY */
            FOREIGN KEY (userid) REFERENCES {$this->table('users')}(id) ON DELETE CASCADE,
            amount FLOAT NOT NULL,
            method VARCHAR(255) NOT NULL,
            detail JSON,
            `date` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP()),
            `status` VARCHAR(100) NOT NULL DEFAULT 'pending'
        )";
    }
}
