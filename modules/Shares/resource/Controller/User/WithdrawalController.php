<?php

namespace Shares\Controller\User;

use AdminDashboard;
use DashboardInterface;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\SQuery\SQuery;
use UserDashboard;
use Uss;
use Alert;
use DashboardFactory;
use RoleImmutable;
use Roles;
use User;
use Ucscode\TreeNode\TreeNode;
use UrlGenerator;

class WithdrawalController implements RouteInterface
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

        $walletInfo = $this->dashboard->getCurrentUser()?->getUserMetaByRegex("wallet\\.");
        $gateways = (new SharesHelper)->getWithdrawalGateways();
        
        $this->handleWithdrawal();

        $this->dashboard->render("@Shares/user/withdrawal.html.twig", [
            "walletInfo" => $walletInfo,
            "gateways" => $gateways,
        ]);
    }

    public function handleWithdrawal(): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wallet_index'])) {

            $user = $this->dashboard->getCurrentUser();
            $balance = $user->getUserMeta("user.balance");
            $amount = (float)$_POST['amount'];

            try {
                if($balance < $amount) {

                    throw new \Exception("Insufficient wallet balance");

                } else if(!$user->isValidPassword($_POST['password'])) {

                    throw new \Exception("Incorrect account password");

                } else {
                    
                    $newBalance = $balance - $amount;
                    $detail = $user->getUserMeta($_POST['wallet_index']);

                    $withdrawal = [
                        'uniqid' => $this->uss->keygen(7),
                        'userid' => $user->getId(),
                        'amount' => $_POST['amount'],
                        'method' => $detail['name'],
                        'detail' => json_encode($detail['detail']),
                        'status' => 'pending',
                    ];

                    $SQL = (new SQuery)
                        ->insert(SharesImmutable::WITHDRAWAL_TABLE, $withdrawal)
                        ->getQuery();

                    $insert = $this->uss->mysqli->query($SQL);

                    if($insert) {
                        $this->processWithdrawal($user, $newBalance, $withdrawal);
                    } else {
                        throw new \Exception("Withdrawal processing failed");
                    }

                }

            } catch(\Exception $e) {
                (new Alert($e->getMessage()))->display();
            }

        }
    }

    protected function processWithdrawal(User $user, float $newBalance, array $item): void
    {
        $user->setUserMeta("user.balance", $newBalance);
        $this->alertAdmins($item);
        $this->sendEmail($item);
        throw new \Exception("<i class='bi bi-check-circle text-success me-1'></i> Withdrawal Successfully processed");
    }

    protected function alertAdmins(array $item): void
    {
        $user = $this->dashboard->getCurrentUser();
        $admins = (new Roles)->getUsersHavingRole(RoleImmutable::ROLE_ADMIN);

        $redirect = new UrlGenerator("/transaction/withdrawal", [
            'search' => $item['uniqid']
        ], AdminDashboard::instance()->config->getBase());

        foreach($admins as $admin) {
            $admin = new User($admin);
            $admin->addNotification([
                'origin' => $user->getId(),
                'model' => 'withdrawal',
                'message' => sprintf(
                    'A member just requested a withdrawal of %s%s payment to the %s wallet',
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
        $emailTable = (new SharesHelper())->buildEmailTable('withdrawal', [
            "Withdrawal Amount" => SHARES_CURRENCY . $item['amount'],
            "Withdrawal Method" => $item['method'],
            "Reference ID" => $item['uniqid'],
            "Status" => $item['status']
        ]);
        $PHPMailer = (new DashboardFactory())->createPHPMailer(false);
        $PHPMailer->addAddress($user->getEmail());
        $PHPMailer->Subject = "Withdrawal Alert";
        $PHPMailer->Body = sprintf("
                <p>Your withdrawal request has been sent. <br> You will be credited once the withdrawal is confirmed.</p>
                <h3>Withdrawal Details</h3>
                <div>%s</div>
                <p>Thank you for choosing %s</p>
            ",
            $emailTable,
            $this->uss->options->get('company:name')
        );
        $PHPMailer->send();
    }

}
