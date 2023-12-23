<?php

namespace Shares\Controller\User;

use CrudIndexManager;
use DashboardInterface;
use RouteInterface;
use UserDashboard;
use Uss;
use CrudActionImmutableInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\TreeNode\TreeNode;

class TransactionLogs implements RouteInterface
{
    protected DashboardInterface $dashboard;
    protected Uss $uss;
    protected CrudIndexManager $crudIndexManager;

    public function __construct(public TreeNode $nav)
    {
        
    }
    
    public function onload(array $matches)
    {
        $this->dashboard = UserDashboard::instance();
        $this->uss = Uss::instance();

        $this->nav->setAttr('active', true);
        
        $this->uss->splitUri(2) == 'withdrawals' ?
            $this->withdrawalManager() :
            $this->depositManager();

        $this->crudIndexManager
            ->setHideBulkActions(true)
            ->setTableWhiteBackground(true)
            ->removeTableColumn("id")
            ->removeTableColumn("userid")
            ->removeWidget(CrudActionImmutableInterface::ACTION_CREATE)
            ->setHideItemActions(true)
        ;

        $this->dashboard->render("@Shares/user/transaction-logs.html.twig", [
            "view" => $this->crudIndexManager->createUI()->getHTML(true),
            "title" => $this->uss->splitUri(2)
        ]);
    }

    protected function depositManager(): void
    {
        $this->crudIndexManager = new CrudIndexManager(SharesImmutable::DEPOSIT_TABLE);

        $this->crudIndexManager
            ->removeTableColumn("detail_snapshot")
            ->removeTableColumn("funded")
            ->setTableColumn("uniqid", "Deposit ID")
            ->removeTableColumn('screenshot')
        ;

        $this->crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = new SharesHelper();
                    //$item['screenshot'] = $helper->getScreenshotElement($item['screenshot']);
                    $item['status'] = $helper->refineStatus($item['status']);
                    $item['amount'] = $helper->currencyFormat($item['amount']);
                    return $item;
                }
            }
        );
    }

    protected function withdrawalManager(): void
    {
        $this->crudIndexManager = new CrudIndexManager(SharesImmutable::WITHDRAWAL_TABLE);

        $this->crudIndexManager
            ->setTableColumn("uniqid", "Withdrawal ID")
        ;

        $this->crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = new SharesHelper();
                    $item['amount'] = $helper->currencyFormat($item['amount']);
                    $item['detail'] = $helper->detailSummary($item['detail']);
                    $item['status'] = $helper->refineStatus($item['status']);
                    return $item;
                }
            }
        );
    }
}
