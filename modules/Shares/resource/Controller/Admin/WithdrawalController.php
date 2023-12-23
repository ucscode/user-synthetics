<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use CrudActionImmutableInterface;
use CrudEditManager;
use CrudEditSubmitInterface;
use CrudIndexManager;
use CrudProcessAutomator;
use DashboardInterface;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\TreeNode\TreeNode;
use Uss;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\UssFormField;
use User;
use UserDashboard;
use DashboardFactory;

class WithdrawalController implements RouteInterface
{
    protected Uss $uss;
    protected DashboardInterface $dashboard;
    protected CrudProcessAutomator $crudProcessAutomator;

    public function __construct(public TreeNode $nav)
    {
        
    }
    public function onload(array $matches)
    {
        $this->dashboard = AdminDashboard::instance();
        $this->uss = Uss::instance();

        $this->nav->setAttr('active', true);
        
        $this->configureCrudManagers();

        $this->dashboard->render("@Shares/admin/withdrawal.html.twig", [
            "view" => $this->crudProcessAutomator->getCreatedUI()
        ]);
    }

    protected function configureCrudManagers(): void
    {
        $this->crudProcessAutomator = new CrudProcessAutomator(SharesImmutable::WITHDRAWAL_TABLE);
        $this->crudProcessAutomator->processAllActions();
        $this->configureIndexManager($this->crudProcessAutomator->getCrudIndexManager());
        $this->configureEditManager($this->crudProcessAutomator->getCrudEditManager());
    }

    protected function configureIndexManager(CrudIndexManager $crudIndexManager): void
    {
        $crudIndexManager
            ->removeTableColumn("userid")
            ->removeTableColumn("id")
            ->setTableColumn("user", "client");

        $crudIndexManager->setTableWhiteBackground(true);
        
        $crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = new SharesHelper();
                    $item['amount'] = $helper->currencyFormat($item['amount']);
                    $item['detail'] = $helper->detailSummary($item['detail']);
                    $item['status'] = $helper->refineStatus($item['status']);
                    $item['user'] = $helper->searchUser($item['userid']);
                    return $item;
                }
            }
        );
    }

    protected function configureEditManager(CrudEditManager $crudEditManager): void
    {
        $crudEditManager
            ->removeField("id")
            ->removeField("userid")
            ->removeField("uniqid");

        $crudEditManager->getField("amount")
            ->setWidgetPrefix("$");

        $crudEditManager->setField(
            "status",
            (new UssFormField(UssForm::NODE_SELECT))
                ->setWidgetOptions([
                    "approved" => "Approved",
                    "pending" => "Pending",
                    "declined" => "Declined",
                ])
                ->setRowAttribute("class", "col-lg-8", true)
        );

        if($crudEditManager->getCurrentAction() === CrudActionImmutableInterface::ACTION_UPDATE) {
            $crudEditManager->removeField("detail");
            $crudEditManager->setModifier(
                new class () implements CrudEditSubmitInterface
                {
                    public function beforeEntry(array $data): array
                    {
                        return $data;
                    }

                    public function afterEntry(bool $status, array $data): bool
                    {
                        if($status) {
                            $user = new User($data['userid']);
                            $this->alertClient($user, $data);
                            $this->sendEmail($user, $data);
                        }
                        return true;
                    }

                    protected function alertClient(User $user, array $data): void
                    {
                        $redirect = UserDashboard::instance()->urlGenerator("/transaction/withdrawals", [
                            "search" => $data['uniqid']
                        ]);

                        $user->addNotification([
                            "model" => 'withdrawal',
                            'userid' => $user->getId(),
                            'message' => sprintf(
                                'your withdrawal request of %s%s to %s wallet is %s',
                                SHARES_CURRENCY,
                                number_format($data['amount'], 2),
                                $data['method'],
                                $data['status']
                            ),
                            'redirect' => $redirect->getResult()
                        ]);
                    }

                    protected function sendEmail(User $user, array $item): void
                    {
                        $emailTable = (new SharesHelper())->buildEmailTable("withdrawal", [
                            "Withdrawal Amount" => SHARES_CURRENCY . number_format($item['amount'], 2),
                            "Withdrawal Method" => $item['method'],
                            "Reference ID" => $item['uniqid'],
                            "Status" => $item['status']
                        ]);
    
                        $PHPMailer = (new DashboardFactory)->createPHPMailer();
                        $PHPMailer->addAddress($user->getEmail());
                        
                        $PHPMailer->Subject = "Withdrawal Approval";
                        $PHPMailer->Body = sprintf("
                            <p>Your withdrawal request has been processed and you have been credited.</p>
                            <h3>Withdrawal Details</h3>
                            <div>%s</div>
                            <p>Thank you for choosing %s</p>
                        ",
                            $emailTable,
                            Uss::instance()->options->get('company:name')
                        );
    
                        $PHPMailer->send();
                    }
                }
            );
        } else {
            $crudEditManager->setReadonlyModifier(
                new class () implements DOMTableInterface {
                    public function foreachItem(array $item): ?array
                    {
                        if(strtolower($item['key']) == 'detail') {
                            $item['value'] = (new SharesHelper())->detailSummary($item['value'], true, false);
                        }
                        return $item;
                    }
                }
            );
        }
    }
}
