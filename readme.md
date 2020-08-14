### Installation

Use composer:

```php
composer require yuanlj-tea/click-captcha
```

### Usage

You can create a captcha with output func:

```php
$captcha = new \ClickCaptcha\Captcha();
$captcha->output();
```

Or inline it directly in the html page:

```php
$inline = $captcha->getInline();
echo "<img src='".$linlie."' />";
```

You'll be able to get the code and compare it with a user input :

```php
$captcha->getCode();
```

### Used in Laravel

Register ServiceProvider and Facade with config/app.php:

```php
'providers' => [
    // ...
    \ClickCaptcha\ClickCaptchaServiceProvider::class,
],
'aliases' => [
    // ...
    'ClickCaptcha' => \ClickCaptcha\ClickCaptchaFacade::class,
],
```

Get a service instance:

Method parameter injection:

```php
use ClickCaptcha\Captcha;

public function getImage(Request $request, Captcha $captcha)
{
  
}
```

Obtained by the facade class:

```php
use ClickCaptcha;

public function getImageV1()
{
     
}
```

By service name:

```php
public function getImageV2()
{
     $captcha = app('click_captcha');
}
```

Check demo:

```php
/**
 * 获取验证码信息
 */
public function getCaptcha()
{
    $key = 'click:captcha:' . str_random(32);

    $captcha = new Captcha();
    $cacheMinutes = 30;

    $inline = $captcha->getInline();
    $data['captcha_key'] = $key;
    $data['expired_at'] = time() + $cacheMinutes * 60;

    $code = $captcha->getCode();

    $data['code'] = $code;
    Cache::put($key, $code, $cacheMinutes);
    $data['image'] = $inline;

    return AjaxResponse::success($data);
}

/**
 * 校验验证码
 * @param Request $request
 */
public function check(Request $request)
{
    $captcha_key = $request->input('captcha_key', '');
    $param = $request->input('data', []);

    if (!Cache::has($captcha_key)) {
        return AjaxResponse::fail('验证码已过期，请刷新后再试');
    }
    if (!is_array($param)) {
        return AjaxResponse::fail('验证失败，参数错误');
    }

    $code = Cache::get($captcha_key);
    $checkCode = array_column($code, 'scope');

    $errKey = $captcha_key . '_error';
    if (Cache::get($errKey) >= 3) {
        Cache::forget($captcha_key);
        Cache::forget($errKey);
        return AjaxResponse::fail('错误次数过多，请刷新验证码后再试');
    }
    foreach ($checkCode as $k => $v) {
        if (!isset($param[$k])) {
          	Cache::increment($errKey);
          	return AjaxResponse::fail('验证失败');
        }

        if (!isset($param[$k]['x']) || !isset($param[$k]['y'])) {
            Cache::increment($errKey);
            return AjaxResponse::fail('验证失败');
        }

        $x = $param[$k]['x'];
        $y = $param[$k]['y'];

        if (
          !(
            $x >= $v['x_limit_left'] && $x <= $v['x_limit_right'] &&
            $y >= $v['y_limit_up'] && $y <= $v['y_limit_down']
          )
        ) {
          Cache::increment($errKey);
          return AjaxResponse::fail('验证失败');
        }
    }
    Cache::forget($errKey);
    return AjaxResponse::success('验证成功');
}
```

## License

MIT