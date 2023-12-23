<?php

namespace Shares\Package;

use Route;
use AdminDashboard;
use AdminDashboardInterface;
use BlockManager;
use CrudActionImmutableInterface;
use Shares\Controller\Admin\DepositController;
use Shares\Controller\Admin\GatewayController;
use Shares\Controller\Admin\SharesCategoryController;
use Shares\Controller\Admin\SharesController;
use Shares\Controller\Admin\InvestmentController;
use Shares\Controller\Admin\WithdrawalController;
use Shares\Controller\Admin\WithdrawalGatewayController;
use SharesImmutable;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssFormField;
use Uss;

class SharesAdminFactory extends AbstractSharesFactory
{
    public function __construct()
    {
        $this->dashboard = AdminDashboard::instance();
        $this->loadSomeComponent();
        $this->index();
        $this->gatewayFactory();
        $this->sharesFactory();
        $this->investmentFactory();
        $this->transactionFactory();
    }

    protected function loadSomeComponent(): void
    {
        $uss = Uss::instance();
        
        if($uss->splitUri(0) == 'admin') {
            $link = new UssElement(UssElement::NODE_LINK);
            $link->setAttribute("href", $uss->abspathToUrl(SharesImmutable::ASSETS_DIR . '/css/admin.css'));
            $link->setAttribute("rel", "stylesheet");
            BlockManager::instance()->appendTo("head_css", "admin-style", $link->getHTML());
        };

        $settingsForm = $this->dashboard->pageRepository->getPageManager(AdminDashboardInterface::PAGE_SETTINGS_DEFAULT)->getForm();

        $settingsForm->addField(
            "company[currency]",
            (new UssFormField())
                ->setWidgetPrefix("Symbol")
                ->setLabelValue("Currency")
                ->setWidgetValue($uss->options->get("company:currency"))
        );
    }

    protected function index(): void
    {
        $indexManager = $this->dashboard->pageRepository->getPageManager(
            AdminDashboardInterface::PAGE_INDEX
        );
        $indexManager->setTemplate('@Shares/admin/index.html.twig');
    }

    protected function gatewayFactory(): void
    {
        $nav = $this->dashboard->menu->add("gateway-nav", [
            "icon" => "bi bi-bank2",
            "label" => "gateway",
            "order" => 3
        ]);

        /** LIST ROUTE */
        $nav->add("gateway-list", [
            "label" => "For Deposit",
            "href" => $this->dashboard->urlGenerator("/gateway")
        ]);

        /** NEW ROUTE */
        $nav->add("gateway-new", [
            "label" => "Add Deposit Gateway",
            "href" => $this->dashboard->urlGenerator("/gateway", [
                "action" => CrudActionImmutableInterface::ACTION_CREATE
            ])
        ]);

        $nav->add("withdrawal-list", [
            "label" => "For Withdrawal",
            "href" => $this->dashboard->urlGenerator("/gateway/cashout")
        ]);

        $nav->add("cashout-new", [
            "label" => "Add Withdrawal Gateway",
            "href" => $this->dashboard->urlGenerator("/gateway/cashout", [
                "action" => CrudActionImmutableInterface::ACTION_CREATE
            ])
        ]);

        new Route($this->path("/gateway(?:/(cashout))?"), new GatewayController($nav));

    }

    protected function sharesFactory(): void
    {
        $nav = $this->dashboard->menu->add("shares-nav", [
            "label" => "Shares",
            "icon" => "bi bi-piggy-bank",
            "order" => 4
        ]);

        $nav->add("category-list", [
            "label" => "View Category",
            "href" => $this->dashboard->urlGenerator("/shares/category")
        ]);

        $nav->add("category-new", [
            "label" => "Add Category",
            "href" => $this->dashboard->urlGenerator("/shares/category", [
                "action" => CrudActionImmutableInterface::ACTION_CREATE
            ])
        ]);

        new Route($this->path("/shares/category"), new SharesCategoryController($nav));

        $nav->add("shares-list", [
            "label" => "View Shares",
            "href" => $this->dashboard->urlGenerator("/shares")
        ]);

        $nav->add("shares-new", [
            "label" => "Add Shares",
            "href" => $this->dashboard->urlGenerator("/shares", [
                "action" => CrudActionImmutableInterface::ACTION_CREATE
            ])
        ]);

        new Route($this->path("/shares"), new SharesController($nav));
    }

    protected function investmentFactory(): void
    {
        $nav = $this->dashboard->menu->add('investment-nav', [
            "label" => "Investment",
            "icon" => "bi bi-currency-exchange",
            "order" => 5,
            "href" => $this->dashboard->urlGenerator("/investments"),
        ]);

        new Route($this->path("/investments"), new InvestmentController($nav));
    }

    protected function transactionFactory(): void
    {
        $nav = $this->dashboard->menu->add("transaction-nav", [
            "label" => 'Transactions',
            "icon" => "bi bi-hourglass",
            "order" => 6,
        ]);

        $nav1 = $nav->add("deposits", [
            "label" => "Deposits",
            "href" => $this->dashboard->urlGenerator("/transaction/deposit")
        ]);

        new Route($this->path("/transaction/deposit"), new DepositController($nav1));

        $nav2 = $nav->add("withdrawals", [
            "label" => "Withdrawals",
            "href" => $this->dashboard->urlGenerator("/transaction/withdrawal")
        ]);

        new Route($this->path("/transaction/withdrawal"), new WithdrawalController($nav2));
    }
}
