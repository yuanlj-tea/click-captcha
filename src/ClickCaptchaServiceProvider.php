<?php

namespace ClickCaptcha;

class ClickCaptchaServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(Captcha::class, function () {
            return new Captcha();
        });

        $this->app->alias(Captcha::class, 'click_captcha');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Captcha::class, 'click_captcha'];
    }
}