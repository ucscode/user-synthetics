<?php

namespace Shares\Controller\User;

use DashboardInterface;
use RouteInterface;
use SharesImmutable;
use Ucscode\SQuery\SQuery;
use UserDashboard;
use Uss;
use Alert;
use Shares\Package\SharesHelper;
use Ucscode\TreeNode\TreeNode;

class WithdrawalSettingsController implements RouteInterface
{
    protected DashboardInterface $dashboard;
    protected Uss $uss;

    public function __construct(public TreeNode $nav)
    {
        
    }
    
    public function onload(array $matches)
    {
        $this->dashboard = UserDashboard::instance();
        $this->uss = Uss::instance();

        $this->nav->setAttr('active', true);
        
        $gateways = (new SharesHelper)->getWithdrawalGateways();

        $this->handleGateway($gateways);

        $this->dashboard->render('@Shares/user/withdrawal-settings.html.twig', [
            'gateways' => $gateways
        ]);
    }

    protected function handleGateway(array $gateways): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_combine($_POST['data']['key'], $_POST['data']['value']);
            
            $gateway = call_user_func(function () use ($gateways) {
                foreach($gateways as $gateway) {
                    if($gateway['id'] == $_POST['entity']) {
                        return $gateway;
                    }
                };
            });

            $key = "wallet.{$gateway['id']}";
            $wallet = [
                "name" => $gateway['method'],
                "detail" => $data,
                "time" => (new \DateTime)->format("Y-m-d H:i:s")
            ];
            
            $user = $this->dashboard->getCurrentUser();
            $updated = $user->setUserMeta($key, $wallet);

            if($updated) {
                $message = sprintf("%s successfully updated", $gateway['method']);
                $display = Alert::DISPLAY_SUCCESS;
            } else {
                $message = sprintf("%s not updated! <br> Please try again", $gateway['method']);
                $display = Alert::DISPLAY_ERROR;
            };

            (new Alert($message))
                ->type(Alert::TYPE_NOTIFICATION)
                ->display($display);
        };
    }
}
