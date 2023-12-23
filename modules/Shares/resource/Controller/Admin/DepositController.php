<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use Alert;
use CrudActionImmutableInterface;
use CrudEditSubmitInterface;
use CrudProcessAutomator;
use DashboardFactory;
use RouteInterface;
use Uss;
use DashboardInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\SQuery\SQuery;
use Ucscode\TreeNode\TreeNode;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssFormField;
use Ucscode\UssForm\UssForm;
use User;
use UserDashboard;

class DepositController implements RouteInterface
{
    protected DashboardInterface $dashboard;
    protected Uss $uss;
    protected CrudProcessAutomator $crudAutomator;

    public function __construct(protected TreeNode $nav)
    {

    }
    public function onload(array $matches)
    {
        $this->dashboard = AdminDashboard::instance();

        $this->nav->setAttr('active', true);

        $this->processCrudAutomator();
        $this->processCrudIndex();
        $this->processCrudEdit();

        $userInterface = $this->crudAutomator->getCreatedUI();

        $this->dashboard->render("@Shares/admin/deposit.html.twig", [
            "view" => $userInterface
        ]);
    }

    protected function processCrudAutomator(): void
    {
        $this->crudAutomator = new CrudProcessAutomator(SharesImmutable::DEPOSIT_TABLE);
        $this->crudAutomator->processIndexAction();
        $this->crudAutomator->processUpdateAction();
        $this->crudAutomator->processDeleteAction();
        $this->crudAutomator->processReadAction();
    }

    protected function processCrudIndex(): void
    {
        $crudIndexManager = $this->crudAutomator->getCrudIndexManager();

        $crudIndexManager
            ->removeTableColumn("id")
            ->removeTableColumn("detail_snapshot")
            ->removeTableColumn("userid")
            ->setTableColumn("user", "client");

        $crudIndexManager->setHideBulkActions(true);
        $crudIndexManager->setTableWhiteBackground(true);
        $crudIndexManager->removeWidget(CrudActionImmutableInterface::ACTION_CREATE);

        $crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = new SharesHelper();
                    $item['screenshot'] = $helper->getScreenshotElement($item['screenshot']);
                    $item['user'] = $helper->searchUser($item['userid']);
                    $item['status'] = $helper->refineStatus($item['status']);
                    $item['funded'] = empty($item['funded']) ? "No" : "Yes";
                    $item['amount'] = $helper->currencyFormat($item['amount']);

                    return $item;
                }
            }
        );
    }

    protected function processCrudEdit(): void
    {
        $crudEditManager = $this->crudAutomator->getCrudEditManager();
        $crudEditManager->removeField("id");
        $crudEditManager->removeField("userid");
        $crudEditManager->removeField("detail_snapshot");
        $crudEditManager->removeField("funded");

        $item = $crudEditManager->getItem();

        $statusField = (new UssFormField(UssForm::NODE_SELECT))
            ->setRowAttribute("class", "col-lg-9", true)
            ->setWidgetOptions([
                "approved" => "Approved",
                "pending" => "Pending",
                "declined" => "Declined",
            ])
            ->setInfoMessage(
                empty($item['funded']) ?
                "If set to approved, the user balance will be funded and the action may not be reversed" :
                "User account has unrevocably been funded from this deposit"
            )
            ->setInfoAttribute("class", empty($item['funded']) ? "text-danger" : "text-primary", true);

        $crudEditManager->setField("status", $statusField);

        $crudEditManager->getField("uniqid")->setReadonly(true);
        $crudEditManager->getField("method")->setReadonly(true);
        $crudEditManager->getField("screenshot")
            ->getRowElement()->setInvisible(true);

        $crudEditManager->setModifier(
            new class () implements CrudEditSubmitInterface {
                public function beforeEntry(array $data): array
                {
                    return $data;
                }

                public function afterEntry(bool $status, array $data): bool
                {
                    $user = new User($data['userid']);

                    if($data['status'] === 'approved' && empty($data['funded'])) {

                        $newBalance = (float)($user->getUserMeta("user.balance") ?? 0);
                        $newBalance += (float)($data['amount']);

                        $funded = $user->setUserMeta("user.balance", $newBalance);

                        if($funded) {

                            $data['funded'] = 1;

                            $SQL = (new SQuery())
                                ->update(SharesImmutable::DEPOSIT_TABLE, $data)
                                ->where("id", $data['id']);

                            Uss::instance()->mysqli->query($SQL);

                            (new Alert("Client account has been funded"))
                                ->type(Alert::TYPE_NOTIFICATION)
                                ->display(Alert::DISPLAY_INFO);
                        }

                    }

                    $this->alertClient($user, $data);
                    $this->sendEmail($user, $data);

                    return true;
                }

                protected function alertClient(User $user, array $item): void
                {
                    $redirect = UserDashboard::instance()->urlGenerator("/transaction/deposits", [
                        'search' => $item['uniqid']
                    ]);

                    $user->addNotification([
                        "model" => 'deposit',
                        'userid' => $user->getId(),
                        'message' => sprintf(
                            'Your deposit of %s%s through %s gateway is %s',
                            SHARES_CURRENCY,
                            number_format($item['amount'], 2),
                            $item['method'],
                            $item['status']
                        ),
                        'redirect' => $redirect->getResult()
                    ]);
                }

                protected function sendEmail(User $user, array $item): void
                {
                    $emailTable = (new SharesHelper())->buildEmailTable("deposit", [
                        "Deposit Amount" => SHARES_CURRENCY . number_format($item['amount'], 2),
                        "Reference ID" => $item['tx_ref'],
                        "Status" => $item['status']
                    ]);

                    $PHPMailer = (new DashboardFactory())->createPHPMailer();
                    $PHPMailer->addAddress($user->getEmail());

                    $PHPMailer->Subject = "Deposit Approval";
                    $PHPMailer->Body = sprintf(
                        "
                        <p>Your deposit request has been confirmed. Your account has been credited</p>
                        <h3>Deposit Detail</h3>
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

        $crudEditManager->setReadonlyModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    if(strtolower($item['key']) == 'screenshot') {
                        $img = new UssElement(UssElement::NODE_IMG);
                        $img->setAttribute("src", $item['value']);
                        $img->setAttribute("width", "100px");
                        $img->setAttribute("class", "img-thumbnail");
                        $item['value'] = "<a href='{$item['value']}' data-glightbox>" . $img->getHTML(1) . "</a>";
                    }
                    return $item;
                }
            }
        );
    }
}
