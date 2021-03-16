<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Attachment;

use Illuminate\Support\ServiceProvider;

/**
 * Class AttachmentServiceProvider
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AttachmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang'),
            ], 'integral-lang');
        }
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'attachment');

        \Larva\Attachment\Models\AttachmentIndex::observe(\Larva\Attachment\Observers\AttachmentIndexObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}