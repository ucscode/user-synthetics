<?php

namespace Shares\Controller\Admin;

use AdminDashboard;
use CrudActionImmutableInterface;
use CrudEditManager;
use CrudEditSubmitInterface;
use CrudIndexManager;
use CrudProcessAutomator;
use DashboardInterface;
use FileUploader;
use RouteInterface;
use Shares\Package\SharesHelper;
use SharesImmutable;
use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\TreeNode\TreeNode;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\UssFormField;
use Uss;
use User;

class GatewayController implements RouteInterface
{
    protected Uss $uss;
    protected DashboardInterface $dashboard;
    protected string $model;

    public const MODEL_DEPOSIT = 'deposit';
    public const MODEL_WITHDRAWAL = 'withdrawal';

    public function __construct(TreeNode $menu)
    {
        $this->uss = Uss::instance();
        $this->dashboard = AdminDashboard::instance();
        (new SharesHelper())->activateNav($menu);
    }

    public function onload(array $matches)
    {
        $this->model = ($matches[1] ?? null) === 'cashout' ? self::MODEL_WITHDRAWAL : self::MODEL_DEPOSIT;

        $this->automateOthers();

        $options = in_array(
            $_GET['action'] ?? null,
            [
                CrudActionImmutableInterface::ACTION_CREATE,
                CrudActionImmutableInterface::ACTION_UPDATE
            ],
            true
        ) ? $this->exposeEditor() : $this->exposeList();

        $options['model'] = $this->model;

        $this->dashboard->render("@Shares/admin/gateway.html.twig", $options);
    }

    protected function exposeEditor(): array
    {
        $crudEditManager = new CrudEditManager(SharesImmutable::GATEWAY_TABLE);

        $item = $this->getCrudItem($crudEditManager);
        $this->handleSubmission($crudEditManager);
        $interface = $crudEditManager->createUI();
        $item = $this->getCrudItem($crudEditManager);

        $crudEditManager->setField(
            "icon",
            (new UssFormField(UssForm::NODE_INPUT, UssForm::TYPE_FILE))
                ->setRowAttribute("class", "d-none", true)
                ->setWidgetAttribute("id", "icon-file")
                ->setWidgetAttribute("accept", "jpg,png,gif,webp,jpeg")
                ->setWidgetAttribute("data-ui-preview-uploaded-image-in", '#icon-img')
                ->setRequired(false)
        );

        $crudEditManager->getField("method")
            ->setWidgetAttribute("placeholder", "Bitcoin, ETH, Bank...")
            ->setLabelValue("Method Name")
            ->setWidgetValue($item['method'] ?? null);

        $statusField = (new UssFormField(UssForm::NODE_INPUT, UssForm::TYPE_SWITCH))
            ->setWidgetValue(1)
            ->setReadonly(false)
            ->setWidgetChecked(!empty($item['status']))
            ->setLabelValue("Enabled")
            ->setRequired(false)
            ->inverse(true);

        $statusField
            ->createSecondaryField("status")
            ->setWidgetValue(0);

        $crudEditManager->setField("status", $statusField);

        return [
            "action" => $_GET['action'] ?? null,
            "view" => $interface,
            "manager" => $crudEditManager,
            "form" => $crudEditManager->getEditForm(),
            "item" => $item,
            "gateway_details" => base64_encode($item['detail'] ?? '[]')
        ];
    }

    protected function exposeList(): array
    {
        $crudIndexManager = new CrudIndexManager(SharesImmutable::GATEWAY_TABLE);

        $crudIndexManager->updateSQuery(function ($SQuery) {
            $SQuery->where("model", $this->model);
            return $SQuery;
        });

        $crudIndexManager->setTableColumns([
            "method",
            "icon",
            "detail" => $this->model == self::MODEL_DEPOSIT ? "detail" : "requirement"
        ]);

        $crudIndexManager->setTableWhiteBackground();
        $crudIndexManager->removeItemAction(CrudActionImmutableInterface::ACTION_READ);
        $crudIndexManager->setDisplayItemActionsAsButton(true);

        $crudIndexManager->setModifier(
            new class ($this->model) implements DOMTableInterface {
                public function __construct(protected string $model)
                {

                }
                public function foreachItem(array $item): ?array
                {
                    $item['icon'] = (new SharesHelper())->getScreenshotElement($item['icon']);
                    $item['detail'] = (new SharesHelper())->detailSummary($item['detail'], $this->model == GatewayController::MODEL_DEPOSIT);

                    return $item;
                }
            }
        );

        return [
            "view" => $crudIndexManager->createUI(),
            "action" => CrudActionImmutableInterface::ACTION_INDEX,
            "manager" => $crudIndexManager,
        ];
    }

    protected function handleSubmission(CrudEditManager $crudEditManager): void
    {
        $crudEditManager->setModifier(
            new class ($this->model) implements CrudEditSubmitInterface {
                protected User $user;
                public function __construct(
                    protected string $model
                ) {
                    $this->user = new User();
                    $this->user->getFromSession();
                }
                public function beforeEntry(array $data): array
                {
                    if(!empty($_FILES['icon']['tmp_name'])) {
                        $icon = $this->uploadFile();
                        if($icon) {
                            $data['icon'] = Uss::instance()->abspathToUrl($icon);
                        }
                    };
                    $data['userid'] = $this->user->getId();
                    $data['model'] = $this->model;
                    $detail = array_combine($data['detail']['key'], $data['detail']['value']);
                    $data['detail'] = json_encode($detail);
                    return $data;
                }

                public function afterEntry(bool $status, array $data): bool
                {
                    return true;
                }

                protected function uploadFile(): ?string
                {
                    $uploader = new FileUploader($_FILES['icon']);
                    $uploader->addMimeType(["image/jpeg", "image/png", "image/gif", "image/jpeg", "image/webp"]);
                    $uploader->setMaxFileSize(1024 * 700);
                    $uploader->setUploadDirectory(SharesImmutable::ASSETS_DIR . "/images/gateway");
                    $result = $uploader->uploadFile();
                    return $result ? $uploader->getUploadedFilepath() : null;
                }
            }
        );
    }

    protected function getCrudItem(CrudEditManager $crudEditManager): array
    {
        $entityId = $crudEditManager->getCurrentEntity();
        if($entityId) {
            $crudEditManager->setItemBy($crudEditManager->getPrimaryKey(), $entityId);
        }
        $item = $crudEditManager->getItem() ?? [];
        return $item;
    }

    protected function automateOthers(): void
    {
        $automator = new CrudProcessAutomator(SharesImmutable::GATEWAY_TABLE);
        $automator->processBulkActions();
        $automator->processDeleteAction();
    }
}
