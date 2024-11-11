<?php

namespace ByHeartLK\OnepayLk;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {

        Setting::query()
            ->whereIn('key', [
                'payment_onepaylk_appId',
                'payment_onepaylk_appHashSalt',
                'payment_onepaylk_appToken',
                'payment_onepaylk_appCallbackToken',
                'payment_onepaylk_nativePhone',
                'payment_onepaylk_nativeEmail',
            ])
            ->delete();
    }
}
