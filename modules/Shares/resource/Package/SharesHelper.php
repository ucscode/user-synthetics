<?php

namespace Shares\Package;

use Ucscode\SQuery\SQuery;
use SharesImmutable;
use Ucscode\DOMTable\DOMTable;
use Ucscode\TreeNode\TreeNode;
use Uss;
use Ucscode\UssElement\UssElement;

class SharesHelper
{
    protected Uss $uss;

    public function __construct()
    {
        $this->uss = Uss::instance();
    }

    public function currencyFormat(?float $amount): string
    {
        $amount = number_format($amount, 2);
        return SHARES_CURRENCY . $amount;
    }

    public function refineStatus(string $status): string
    {
        $span = new UssElement(UssElement::NODE_SPAN);
        switch(strtolower(trim($status))) {
            case "active":
            case "approved":
            case "paid":
                $color = "bg-success";
                break;
            case "pending":
                $color = "bg-warning";
                break;
            case "declined":
                $color = "bg-danger";
                break;
            default:
                $color = "bg-secondary";
        }
        $span->setAttribute('class', 'badge ' .  $color);
        $span->setContent($status);
        return $span->getHTML(true);
    }

    public function getWithdrawalGateways(): array
    {
        $SQL = (new SQuery())
            ->select()
            ->from(SharesImmutable::GATEWAY_TABLE)
            ->where("model", "withdrawal")
            ->and("status", 1);

        $result = $this->uss->mysqli->query($SQL);

        return $this->uss->mysqliResultToArray($result, function ($value, $key) {
            return ($key === 'detail') ? json_decode($value, true) : $value;
        });
    }

    public function getDepositGateways(): array
    {
        $SQL = (new SQuery())
            ->select()
            ->from(SharesImmutable::GATEWAY_TABLE)
            ->where("status", 1)
            ->and("model", "deposit");

        $result = $this->uss->mysqli->query($SQL);

        if($result) {
            $result = $this->uss->mysqliResultToArray($result);
            foreach($result as $key => $data) {
                $result[$key]['detail'] = json_decode($data['detail'], true);
            }
        };

        return $result ?? [];
    }

    public function detailSummary(string $detail, bool $both = true, bool $clamp = true): string
    {
        $detail = json_decode($detail);
        $block = new UssElement(UssElement::NODE_UL);
        $block->setAttribute("class", "m-0 ps-3 " . ($clamp ? "line-clamp clamp-2" : null));
        foreach($detail as $key => $value) {
            $li = new UssElement(UssElement::NODE_LI);
            $li->setContent($both ? "{$key}: {$value}" : $key);
            $block->appendChild($li);
        }
        return $block->getHTML(true);
    }

    public function getScreenshotElement(?string $src, string $else = null): string
    {
        if(!empty($src)) {
            $anchor = new UssElement(UssElement::NODE_A);
            $anchor->setAttribute("href", $src);
            $anchor->setAttribute("data-glightbox", '');
            $el = new UssElement(UssElement::NODE_IMG);
            $el->setAttribute("src", $src);
            $el->setAttribute("width", "80px");
            $el->setAttribute("class", "img-thumbnail");
            $anchor->appendChild($el);
            return $anchor->getHTML(true);
        };
        return $else ? $else : '<i class="bi bi-question-circle text-danger"></i>';
    }

    public function searchUser(?int $userid): ?string
    {
        if($userid) {
            $user = new \User($userid);
            $el = new UssElement(UssElement::NODE_A);
            $el->setAttribute("href", \AdminDashboard::instance()->urlGenerator("/users", [
                "search" => $user->getEmail()
            ]));
            $el->setContent($user->getEmail());
            return $el->getHTML(true);
        };
        return null;
    }

    public function activateNav(TreeNode $node): void
    {
        foreach($node->children as $menu) {
            $nodeHref = $menu->getAttr('href')->getResult(true);
            $menu->setAttr('active', $nodeHref == $_SERVER['REQUEST_URI']);
        }
    }

    public function buildEmailTable(string $name, array $content): string
    {
        $table = new DOMTable($name);
        $table->setColumns(['key' => '', 'value' => '']);
        $data = [];
        foreach($content as $key => $value) {
            $data[] = ['key' => $key, 'value' => $value];
        }
        $table->setData($data);
        $element = $table->build();
        $thead = $table->getTheadElement();
        $thead->getParentElement()->removeChild($thead);
        return $element->getHTML(true);
    }
}
