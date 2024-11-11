<?php

namespace ByHeartLK\OnepayLk\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class OnepayLkServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (! is_plugin_active('payment')) {
            return;
        }

        $this->setNamespace('plugins/onepaylk')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);
    }
}
