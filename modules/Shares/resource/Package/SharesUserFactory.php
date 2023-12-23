<?php

namespace Shares\Package;

use UserDashboard;
use Route;
use Shares\Controller\User\DepositController;
use Shares\Controller\User\WithdrawalController;
use Shares\Controller\User\SharesCollection;
use Shares\Controller\User\SharesItems;
use Shares\Controller\User\TransactionLogs;
use Shares\Controller\User\WithdrawalSettingsController;
use UserDashboardInterface;

class SharesUserFactory extends AbstractSharesFactory
{
    public function __construct()
    {
        $this->dashboard = UserDashboard::instance();
        $this->index();
        $this->walletFactory();
        $this->sharesFactory();
        $this->transactionFactory();
        $this->withdrawalSettingsFactory();
    }

    protected function index(): void
    {
        $indexManager = $this->dashboard->pageRepository->getPageManager(
            UserDashboardInterface::PAGE_INDEX
        );
        $indexManager->setTemplate("@Shares/user/index.html.twig");
    }

    protected function walletFactory(): void
    {
        $nav = $this->dashboard->menu->add("wallet-nav", [
            "label" => "wallet",
            "icon" => "bi bi-wallet",
            "order" => 3,
        ]);

        $nav1 = $nav->add("deposit", [
            "label" => "Deposit Fund",
            "href" => $this->dashboard->urlGenerator("/deposit")
        ]);

        new Route($this->path("/deposit"), new DepositController($nav1));

        $nav2 = $nav->add('withdrawal', [
            "label" => "withdraw Fund",
            "href" => $this->dashboard->urlGenerator("/withdrawal")
        ]);

        new Route($this->path("/withdrawal"), new WithdrawalController($nav2));
    }

    protected function sharesFactory(): void
    {
        $nav = $this->dashboard->menu->add("shares-nav", [
            "label" => "shares",
            "icon" => "bi bi-currency-exchange",
            "order" => 4,
        ]);

        $nav1 = $nav->add("buy-shares", [
            "label" => "buy shares",
            "href" => $this->dashboard->urlGenerator("/shares/collection")
        ]);

        new Route($this->path("/shares/collection"), new SharesCollection($nav1));

        $nav2 = $nav->add("shares", [
            "label" => "Shares",
            "href" => $this->dashboard->urlGenerator("/shares")
        ]);

        new Route($this->path("/shares"), new SharesItems($nav2));
    }

    protected function transactionFactory(): void
    {
        $nav = $this->dashboard->menu->add("transaction-nav", [
            "label" => "Transactions",
            "icon" => "bi bi-arrow-left-right",
            "order" => 5,
        ]);

        $nav1 = $nav->add("deposits", [
            "label" => "deposits",
            "href" => $this->dashboard->urlGenerator("/transaction/deposits")
        ]);

        new Route($this->path("/transaction/deposits"), new TransactionLogs($nav1));

        $nav2 = $nav->add("withdrawals", [
            "label" => "withdrawals",
            "href" => $this->dashboard->urlGenerator("/transaction/withdrawals")
        ]);

        new Route($this->path("/transaction/withdrawals"), new TransactionLogs($nav2));
    }

    protected function withdrawalSettingsFactory(): void
    {
        $nav = $this->dashboard->profileBatch->add("payment", [
            "label" => "Withdrawal Method",
            "icon" => "bi bi-bank",
            "order" => 2,
            "href" => $this->dashboard->urlGenerator("/payment"),
        ]);

        new Route($this->path("/payment"), new WithdrawalSettingsController($nav));
    }
}
