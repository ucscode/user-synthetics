<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use AdminDashboardInterface;
use CrudActionImmutableInterface;
use CrudEditSubmitInterface;
use CrudProcessAutomator;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\SQuery\SQuery;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\UssFormField;
use Uss;
use Symfony\Component\Uid\Uuid;
use Ucscode\DOMTable\DOMTableInterface;
use User;
use Ucscode\TreeNode\TreeNode;

class SharesController implements RouteInterface
{
    public CrudProcessAutomator $crudAutomator;
    public AdminDashboardInterface $dashboard;

    public function __construct(TreeNode $node)
    {
        (new SharesHelper())->activateNav($node);
    }

    public function onload(array $matches)
    {
        $this->processAutomator();
        $this->dashboard = AdminDashboard::instance();
        $this->processCrudIndex();
        $this->processCrudEdit();

        $this->dashboard->render("@Shares/admin/shares.html.twig", [
            "view" => $this->crudAutomator->getCreatedUI()
        ]);
    }

    public function processAutomator(): void
    {
        $this->crudAutomator = new CrudProcessAutomator(SharesImmutable::SHARES_TABLE);
        $this->crudAutomator->processAllActions();
    }

    protected function processCrudIndex(): void
    {
        $crudIndexManager = $this->crudAutomator->getCrudIndexManager();
        $crudIndexManager->setTableWhiteBackground();
        $crudIndexManager
            ->removeTableColumn("userid")
            ->removeTableColumn("subgroup_id")
            ->removeTableColumn("group_id")
            ->removeTableColumn("id")
            ->removeTableColumn("date");

        $crudIndexManager->setModifier(
            new class () implements DOMTableInterface {
                public function foreachItem(array $item): ?array
                {
                    $helper = (new SharesHelper());
                    $item['credit_bonus'] = $helper->currencyFormat($item['credit_bonus']);
                    $item['daily_increment'] .= "%";
                    $item['end_after_day'] .= " days";
                    $item['min_amount'] = $helper->currencyFormat($item['min_amount']);
                    $item['status'] = $helper->refineStatus($item['status']);
                    return $item;
                }
            }
        );
    }

    protected function processCrudEdit(): void
    {
        $crudEditManager = $this->crudAutomator->getCrudEditManager();

        $crudEditManager->removeField("id");
        $crudEditManager->removeField("uuid");
        $crudEditManager->removeField("userid");
        $crudEditManager->removeField("date");

        $this->fetchGroup("group_id");
        $this->fetchGroup("subgroup_id");

        $crudEditManager->getField("end_after_day")
            ->setInfoMessage("Set to zero for unlimited");

        $statusField = (new UssFormField(UssForm::NODE_SELECT))
            ->setWidgetOptions([
                "inactive" => "Inactive",
                "active" => "Active"
            ])
            ->setRowAttribute("class", "col-lg-8", true);

        $crudEditManager->setField("status", $statusField);

        $crudEditManager->setModifier(
            new class () implements CrudEditSubmitInterface {
                protected User $user;

                public function __construct()
                {
                    $this->user = (new User())->getFromSession();
                }

                public function beforeEntry(array $data): array
                {
                    $data['uuid'] = Uuid::v4();
                    if(($_GET['action'] ?? null) === CrudActionImmutableInterface::ACTION_CREATE) {
                        $data['userid'] = $this->user->getId();
                    }
                    return $data;
                }

                public function afterEntry(bool $status, array $data): bool
                {
                    return true;
                }
            }
        );
    }

    protected function fetchGroup(string $fieldname): void
    {
        $uss = Uss::instance();

        $SQL = (new SQuery())
            ->select()
            ->from(DB_PREFIX . "shares_group");

        $result = $uss->mysqli->query($SQL);
        $values = ['' => '-- select --'];

        foreach($uss->mysqliResultToArray($result) as $item) {
            $key = $item['id'];
            $value = $item['group_name'];
            $values[$key] = $value;
        }

        $field = (new UssFormField(UssForm::NODE_SELECT))
            ->setWidgetOptions($values)
            ->setLabelValue(ucwords(str_replace("_id", "", $fieldname)))
            ->setRowAttribute("class", "col-lg-8", true);

        if(count($values) === 1) {
            $anchor = new UssElement(UssElement::NODE_A);
            $anchor->setAttribute("href", $this->dashboard->urlGenerator(
                "shares/category",
                [
                    'action' => CrudActionImmutableInterface::ACTION_CREATE
                ]
            ));
            $anchor->setContent('Click here');
            $field
                ->setInfoMessage($anchor->getHTML() . " to add new group item")
                ->setInfoAttribute("class", "mb-2", true);
        }
        $this->crudAutomator->getCrudEditManager()->setField($fieldname, $field);
    }
}
