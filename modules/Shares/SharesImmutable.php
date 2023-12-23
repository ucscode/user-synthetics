<?php

final class SharesImmutable
{
    public const DIR = __DIR__;
    
    public const TEMPLATE_DIR = self::DIR . "/template";
    public const ASSETS_DIR = self::DIR . "/assets";
    public const RES_DIR = self::DIR . "/resource";

    public const IMAGE_MIMES = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/webp",
        "image/gif",
    ];

    public const INVESTMENT_TABLE = DB_PREFIX . "investments";
    public const DEPOSIT_TABLE = DB_PREFIX . "deposit";
    public const GATEWAY_TABLE = DB_PREFIX . "gateway";
    public const SHARES_GROUP_TABLE = DB_PREFIX . "shares_group";
    public const SHARES_TABLE = DB_PREFIX . "shares";
    public const WITHDRAWAL_TABLE = DB_PREFIX . "withdrawal";
}
