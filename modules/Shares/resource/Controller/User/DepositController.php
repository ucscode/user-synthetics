<?php

namespace Shares\Controller\User;

use AdminDashboard;
use Alert;
use DashboardFactory;
use DashboardInterface;
use FileUploader;
use RoleImmutable;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use UserDashboard;
use Ucscode\SQuery\SQuery;
use Ucscode\TreeNode\TreeNode;
use Uss;
use Roles;
use Ucscode\DOMTable\DOMTable;
use UrlGenerator;
use User;

class DepositController implements RouteInterface
{
    public DashboardInterface $dashboard;
    public Uss $uss;

    public function __construct(public TreeNode $nav)
    {

    }

    public function onload(array $matches)
    {
        $this->dashboard = UserDashboard::instance();
        $this->uss = Uss::instance();

        $this->nav->setAttr('active', true);

        $gateways = (new SharesHelper())->getDepositGateways();

        $this->handleDepositRequest($gateways);

        $this->dashboard->render("@Shares/user/deposit.html.twig", [
            "gateways" => $gateways
        ]);
    }

    protected function handleDepositRequest(array $gateways): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item = $_POST;
            try {

                if(!empty($_FILES['screenshot']['tmp_name'])) {
                    $item['screenshot'] = $this->uploadScreenshot();
                }

                $item['uniqid'] = $this->uss->keygen(7);
                $item['userid'] = $this->dashboard->getCurrentUser()->getId();
                $item['detail_snapshot'] = call_user_func(function ($item_id) use ($gateways) {
                    $result = [];
                    foreach($gateways as $data) {
                        if($data['id'] == $item_id) {
                            $result = $data;
                            break;
                        }
                    };
                    return json_encode($result);
                }, $item['detail_snapshot']);
                $item['status'] = 'pending';

                $SQL = (new SQuery())
                    ->insert(SharesImmutable::DEPOSIT_TABLE, $item)
                    ->getQuery();

                $errorMsg = "Your deposit of {$item['amount']} {$item['method']} failed. <br> Please try again or contact the admin if the problem proceeds";

                $result = $this->uss->mysqli->query($SQL);

                if($result) {

                    $message = sprintf(
                        "Your deposit of <span class='text-primary'>%s%s %s</span> was successful. <br> You will funded once the transaction has been confirmed",
                        SHARES_CURRENCY,
                        number_format($item['amount'], 2),
                        $item['method']
                    );

                    $this->notifyAdmin($item);
                    $this->sendEmail($item);

                } else {
                    $message = $errorMsg;
                }

                (new Alert($message))->display();

            } catch(\Exception $e) {

                (new Alert($e->getMessage()))->display();

            }
        }
    }

    protected function uploadScreenshot(): ?string
    {
        $uploader = new FileUploader($_FILES['screenshot']);
        $uploader->addMimeType(SharesImmutable::IMAGE_MIMES);
        $uploader->setUploadDirectory(SharesImmutable::ASSETS_DIR . "/images/deposits");
        $uploader->setMaxFileSize(1024 * 1024);
        $user = $this->dashboard->getCurrentUser();
        $uploader->setFilenamePrefix($user->getId());
        if($uploader->uploadFile()) {
            return $this->uss->abspathToUrl($uploader->getUploadedFilepath());
        }
        throw new \Exception("Screenshot upload failed! <br> " . $uploader->getError(true));
    }

    protected function notifyAdmin(array $item): void
    {
        $user = $this->dashboard->getCurrentUser();
        $admins = (new Roles())->getUsersHavingRole(RoleImmutable::ROLE_ADMIN);

        $redirect = new UrlGenerator("/transaction/deposit", [
            'search' => $item['uniqid']
        ], AdminDashboard::instance()->config->getBase());

        foreach($admins as $admin) {
            $admin = new User($admin);
            $admin->addNotification([
                'origin' => $user->getId(),
                'model' => 'deposit',
                'userid' => $admin->getId(),
                'message' => sprintf(
                    "A member just deposit %s%s through the %s gateway.",
                    SHARES_CURRENCY,
                    $item['amount'],
                    $item['method']
                ),
                'redirect' => $redirect->getResult()
            ]);
        }
    }

    protected function sendEmail(array $item): void
    {
        $user = $this->dashboard->getCurrentUser();
        $emailTable = (new SharesHelper())->buildEmailTable('deposit', [
            "Transaction Amount" => SHARES_CURRENCY . number_format($item['amount'], 2),
            "Transaction Method" => $item['method'],
            "Reference ID" => $item['tx_ref'],
            "Status" => $item['status']
        ]);
        $PHPMailer = (new DashboardFactory())->createPHPMailer(false);
        $PHPMailer->addAddress($user->getEmail());
        $PHPMailer->Subject = "Deposit Alert";
        $PHPMailer->Body = sprintf("
                <p>Your deposit request has been sent. <br> You will be credited once the deposit is confirmed.</p>
                <h3>Deposit Details</h3>
                <div>%s</div>
                <p>Thank you for choosing %s</p>
            ",
            $emailTable,
            $this->uss->options->get('company:name')
        );
        $PHPMailer->send();
    }
}
