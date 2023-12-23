<?php

namespace Shares\Controller\User;

use CrudActionImmutableInterface;
use CrudIndexManager;
use DashboardInterface;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use UserDashboard;
use Ucscode\TreeNode\TreeNode;

class SharesItems implements RouteInterface
{
    protected DashboardInterface $dashboard;
    protected CrudIndexManager $crudIndexManager;

    public function __construct(public TreeNode $nav)
    {
        
    }
    
    public function onload(array $matches)
    {
        $this->dashboard = UserDashboard::instance();
        $this->nav->setAttr('active', true);
        $this->createIndexManager();
        $this->dashboard->render("@Shares/user/shares-items.html.twig", [
            "output" => $this->crudIndexManager->createUI()->getHTML(true)
        ]);
    }

    protected function createIndexManager(): void
    {
        $this->crudIndexManager = new CrudIndexManager(SharesImmutable::INVESTMENT_TABLE);

        $this->crudIndexManager->setTableColumns([
            "uniqid" => "Investment ID",
            "title" => "Package",
            "shares_amount" => "Invested",
            "profit_amount" => "Earned",
            "daily_increment" => "% per day",
            "end_after_day" => "expiry",
            "days_elapsed" => "spent",
            "status"
        ]);

        $this->crudIndexManager->setHideBulkActions(true);
        $this->crudIndexManager->setTableWhiteBackground(true);
        $this->crudIndexManager->removeWidget(CrudActionImmutableInterface::ACTION_CREATE);
        $this->crudIndexManager->setHideItemActions(true);

        $this->crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {   
                    $helper = new SharesHelper();
                    $item['shares_amount'] = $helper->currencyFormat($item['shares_amount']);
                    $item['profit_amount'] = $helper->currencyFormat($item['profit_amount']);
                    $item['daily_increment'] .= "%";
                    $item['end_after_day'] .= " days";
                    $item['days_elapsed'] .= " days";
                    $item['status'] = $helper->refineStatus($item['status']);
                    return $item;
                }
            }
        );  
    }
}
