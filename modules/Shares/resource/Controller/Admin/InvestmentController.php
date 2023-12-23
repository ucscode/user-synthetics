<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use CrudActionImmutableInterface;
use CrudProcessAutomator;
use DashboardInterface;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\SQuery\SQuery;
use Ucscode\TreeNode\TreeNode;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\UssFormField;
use Uss;
use User;

class InvestmentController implements RouteInterface
{
    protected DashboardInterface $dashboard;
    protected Uss $uss;
    protected CrudProcessAutomator $crudProcessAutomator;

    public function __construct(protected TreeNode $nav)
    {}

    public function onload(array $matches)
    {
        $this->dashboard = AdminDashboard::instance();
        $this->uss = Uss::instance();

        $this->nav->setAttr('active', true);

        $this->useCrudOperator();

        $this->dashboard->render("@Shares/admin/investments.html.twig", [
            "view" => $this->crudProcessAutomator->getCreatedUI()
        ]);
    }

    protected function useCrudOperator(): void
    {
        $this->crudProcessAutomator = new CrudProcessAutomator(SharesImmutable::INVESTMENT_TABLE);
        $this->crudProcessAutomator->processAllActions();
        $this->crudEditManager();
        $this->crudIndexManager();
    }

    protected function crudEditManager(): void
    {
        $crudEditManager = $this->crudProcessAutomator->getCrudEditManager();

        $unwantedFields = [
            "id",
            "group_id",
            "subgroup_id",
            "total_increment",
            "days_elapsed",
            "activation_date",
            "last_increment_date",
            "date",
            "profit_amount",
        ];

        if($crudEditManager->getCurrentAction() === CrudActionImmutableInterface::ACTION_CREATE) {
            $unwantedFields[] = "uniqid";
            $unwantedFields[] = "shares_uuid";
        } else {
            $crudEditManager->getField("uniqid")->setReadonly(true);
            $crudEditManager->getField("shares_uuid")
                ->setReadonly(true)
                ->setRequired(false);
            $unwantedFields[] = "userid";
        }

        foreach($unwantedFields as $fieldName) {
            $crudEditManager->removeField($fieldName);
        }

        $crudEditManager->setField(
            "userid",
            (new UssFormField(UssForm::NODE_SELECT))
                ->setRowAttribute("class", "col-lg-8", true)
                ->setWidgetOptions($this->getAllUsers())
                ->setLabelValue("User")
        );

        $crudEditManager->getField("shares_amount")
            ->setLabelValue("Investment")
            ->setWidgetPrefix("$");

        // $crudEditManager->getField("profit_amount")
        //     ->setLabelValue("Earnings")
        //     ->setWidgetPrefix("$")
        //     ->setReadonly(true);

        $crudEditManager->setField(
            "status",
            (new UssFormField(UssForm::NODE_SELECT))
                ->setWidgetOptions([
                    "active" => "Active",
                    "pending" => "Pending",
                    "inactive" => "Inactive"
                ])
                ->setRowAttribute('class', 'col-lg-8', true)
        );
    }

    protected function crudIndexManager(): void
    {
        $crudIndexManager = $this->crudProcessAutomator->getCrudIndexManager();

        $crudIndexManager->setTableColumns([
            "uniqid",
            "shares_uuid" => "uuid",
            "user",
            "title",
            "shares_amount" => "invested",
            "profit_amount" => "Earnings",
            "daily_increment" => "Daily Increment",
            "total_increment" => "accumulated %",
            "end_after_day" => "Expiry",
            "days_elapsed" => "spent",
            "status",
        ]);

        $crudIndexManager->setTableWhiteBackground(true);
        
        $crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = new SharesHelper();
                    $item['user'] = $helper->searchUser($item['userid']);
                    $item['shares_amount'] = $helper->currencyFormat($item['shares_amount']);
                    $item['profit_amount'] = $helper->currencyFormat($item['profit_amount']);
                    $item['daily_increment'] .= "%";
                    $item['total_increment'] .= "%";
                    $item['end_after_day'] .= " days";
                    $item['days_elapsed'] .= " days";
                    $item['status'] = $helper->refineStatus($item['status']);
                    return $item;
                }
            }
        );
    }

    protected function getAllUsers(): array
    {
        $SQL = (new SQuery())
            ->select(['id', 'email'])
            ->from(DB_PREFIX . "users")
            ->getQuery();

        $result = $this->uss->mysqli->query($SQL);

        $data = [];

        while($item = $result->fetch_assoc()) {
            $data[ $item['id'] ] = $item['email'];
        };

        return $data;
    }
}
