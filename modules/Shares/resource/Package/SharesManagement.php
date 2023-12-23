<?php

namespace Shares\Package;

use SharesImmutable;
use Ucscode\SQuery\SQuery;
use Uss;
use User;
use DateTime;

/**
 * Referral Program
 * Admin credit & Debit User
 * Verification of User, KYC
 */

final class SharesManagement
{
    protected Uss $uss;

    public function __construct()
    {
        $this->uss = Uss::instance();
        $this->uss->addGlobalTwigOption("currency", SHARES_CURRENCY);
        $this->setDefaults();
        $this->activateInvestment();
        $this->incrementInvestment();
    }

    protected function setDefaults(): void
    {
        $defaults = [
            "company:currency" => "$"
        ];

        foreach($defaults as $key => $default) {
            $value = $this->uss->options->get($key);
            if(is_null($value)) {
                $this->uss->options->set($key, $default);
            }
        }
    }

    protected function activateInvestment(): void
    {
        $SQL = (new SQuery())->select()
            ->from(SharesImmutable::INVESTMENT_TABLE)
            ->where('status', 'static');

        $result = $this->uss->mysqli->query($SQL);

        if($result->num_rows) {
            while($item = $result->fetch_assoc()) {
                $amount = (float)$item['shares_amount'];
                $user = new User($item['userid']);
                $incrementDate = (new DateTime())->format("Y-m-d H:i:s");
                $updater = [
                    'activation_date' => $incrementDate,
                    'last_increment_date' => $incrementDate,
                    'status' => 'active',
                ];
                $newBalance = (float)$user->getUserMeta("user.balance") - $amount;
                if($user->setUserMeta("user.balance", $newBalance)) {
                    $SQL = (new SQuery())
                        ->update(SharesImmutable::INVESTMENT_TABLE, $updater)
                        ->where('id', $item['id']);
                    $this->uss->mysqli->query($SQL);
                }
            }
        }
    }

    protected function incrementInvestment(): void
    {
        $SQL = (new SQuery())
            ->select()
            ->from(SharesImmutable::INVESTMENT_TABLE)
            ->where('status', 'active')
            ->and(
                "DATEDIFF(CURDATE(), last_increment_date)",
                0,
                ">",
                SQuery::FILTER_IGNORE
            )
            ->getQuery();

        $result = $this->uss->mysqli->query($SQL);

        if($result->num_rows) {
            while($item = $result->fetch_assoc()) {
                $last_increment_date = new DateTime($item['last_increment_date']);
                $days_elapsed = $last_increment_date->diff(new DateTime())->days;
                $total_elapse = $days_elapsed + (int)$item['days_elapsed'];

                if(!empty($item['end_after_day']) && $total_elapse > (int)$item['end_after_day']) {
                    $total_elapse = (int)$item['end_after_day'];
                    $item['status'] = 'expired';
                }

                $updater = [
                    "days_elapsed" => $total_elapse,
                    "total_increment" => (float)$item['daily_increment'] * $total_elapse,
                    "last_increment_date" => (new DateTime())->format("Y-m-d H:i:s"),
                ];

                $updater["profit_amount"] = (float)$item['shares_amount'] * $updater['total_increment'] * 0.01;

                $SQL = (new SQuery())
                    ->update(SharesImmutable::INVESTMENT_TABLE, $updater)
                    ->where("id", $item['id']);

                $this->uss->mysqli->query($SQL);
            }
        }
    }
}
