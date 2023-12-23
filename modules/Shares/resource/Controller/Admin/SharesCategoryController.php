<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use AdminDashboardInterface;
use CrudActionImmutableInterface;
use CrudProcessAutomator;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\TreeNode\TreeNode;

class SharesCategoryController implements RouteInterface
{
    protected CrudProcessAutomator $crudAutomator;
    protected AdminDashboardInterface $dashboard;

    public function __construct(TreeNode $node)
    {
        (new SharesHelper())->activateNav($node);
    }

    public function onload(array $matches)
    {
        $this->dashboard = AdminDashboard::instance();
        $this->crudProcessor();
        $this->crudIndexProcessor();
        $this->crudEditProcessor();
        $interface = $this->crudAutomator->getCreatedUI();

        $this->dashboard->render('@Shares/admin/shares-category.html.twig', [
            'view' => $interface
        ]);
    }

    protected function crudProcessor(): void
    {
        $this->crudAutomator = new CrudProcessAutomator(SharesImmutable::SHARES_GROUP_TABLE);
        $this->crudAutomator->processAllActions();
    }

    protected function crudIndexProcessor(): void
    {
        $crudIndexManager = $this->crudAutomator->getCrudIndexManager();
        $crudIndexManager->removeTableColumn("id");
        $crudIndexManager->setTableWhiteBackground(true);
        $crudIndexManager->setDisplayItemActionsAsButton(true);
        $crudIndexManager->removeItemAction(CrudActionImmutableInterface::ACTION_READ);
    }

    protected function crudEditProcessor(): void
    {
        $crudEditManager = $this->crudAutomator->getCrudEditManager();
        $crudEditManager->removeField("id");
    }
}
