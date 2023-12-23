<?php

namespace Shares\Controller\User;

use DashboardInterface;
use RouteInterface;
use Ucscode\SQuery\SQuery;
use UserDashboard;
use Uss;
use Alert;
use Exception;
use SharesImmutable;
use Ucscode\TreeNode\TreeNode;

class SharesCollection implements RouteInterface
{
    public DashboardInterface $dashboard;
    public Uss $uss;

    public function __construct(public TreeNode $nav)
    {
        
    }

    public function onload(array $matches)
    {
        $this->uss = Uss::instance();
        $this->dashboard = UserDashboard::instance();

        $this->nav->setAttr('active', true);
        
        $error = $this->handleInvestmentRequest();

        $this->dashboard->render("@Shares/user/shares-collection.html.twig", [
            "shares_list" => $this->getShares(),
            "error" => $error,
        ]);
    }

    protected function handleInvestmentRequest(): ?string
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $shares = $this->getShares($_POST['shares_uuid']);

            try {

                if((float)$_POST['shares_amount'] < (float)$shares['min_amount']) {
                    throw new \Exception(
                        sprintf(
                            "The mininum investment amount for %s package is %s",
                            $shares['title'],
                            $shares['min_amount']
                        )
                    );
                }

                $userBalance = (float)($this->dashboard->getCurrentUser()->getUserMeta("user.balance") ?? 0);

                if((float)$_POST['shares_amount'] > $userBalance) {
                    throw new \Exception("Insufficient wallet balance");
                }

                $investment = $this->createInvestment($shares);

                $SQL = (new SQuery())
                    ->insert(SharesImmutable::INVESTMENT_TABLE, $investment)
                    ->getQuery();

                $insert = $this->uss->mysqli->query($SQL);

                if($insert) {
                    $location = $this->dashboard->urlGenerator("/shares");

                    (new Alert())
                        ->setOption(
                            "message",
                            sprintf("Your %s package has been created", $shares['title'])
                        )
                        ->followRedirectAs("shares:package")
                        ->display();

                    header("location: {$location}");
                    exit;
                }

                throw new Exception(
                    sprintf(
                        "The request failed! <br> %s package could not be created",
                        $shares['title']
                    )
                );

            } catch(\Exception $e) {

                return $e->getMessage();

            }

        }

        return null;
    }

    protected function createInvestment(array $shares): array
    {
        $investment = [
            "uniqid" => $this->uss->keygen(7),
            "shares_uuid" => $shares['uuid'],
            "userid" => $this->dashboard->getCurrentUser()->getId(),
            "title" => $shares['title'],
            "shares_amount" => $_POST['shares_amount'],
            "group_id" => $shares['group_id'],
            "subgroup_id" => $shares['subgroup_id'],
            "credit_bonus" => $shares['credit_bonus'],
            "daily_increment" => $shares['daily_increment'],
            "end_after_day" => $shares['end_after_day'],
        ];
        return $investment;
    }

    protected function getShares(?string $uuid = null): array
    {
        $SQL = (new SQuery())
            ->select()
            ->from(SharesImmutable::SHARES_TABLE);

        $result = $this->uss->mysqli->query($SQL);
        $result = $this->uss->mysqliResultToArray($result);

        $refactor = [
            "group_id" => 'group',
            "subgroup_id" => 'subgroup'
        ];

        foreach($result as &$data) {
            foreach($refactor as $key => $name) {
                $value = $data[$key];
                $item = $this->uss->fetchItem(SharesImmutable::SHARES_GROUP_TABLE, $value);
                $data[$name] = $item['group_name'];
            };
        }

        return is_null($uuid) ? $result : call_user_func(function () use ($uuid, $result) {
            foreach($result as $data) {
                if($data['uuid'] ===  $uuid) {
                    return $data;
                }
            };
            return [];
        });
    }
}
