<?php

namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Anomaly\ThrottleSecurityCheckExtension\ThrottleSecurityCheckExtension;
use Anomaly\UsersModule\User\UserAuthenticator;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class ThrottleLogin
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class ThrottleLogin
{
    /**
     * Handle the command.
     *
     * @param  Repository                     $cache
     * @param  Request                        $request
     * @param  UserAuthenticator              $authenticator
     * @param  SettingRepositoryInterface     $settings
     * @param  ThrottleSecurityCheckExtension $extension
     * @return bool
     */
    public function handle(
        Repository $cache,
        Request $request,
        UserAuthenticator $authenticator,
        SettingRepositoryInterface $settings,
        ThrottleSecurityCheckExtension $extension
    ) {
        $maxAttempts = $settings->value('anomaly.extension.throttle_security_check::max_attempts', 5);

        $lockout = (int)$settings->value('anomaly.extension.throttle_security_check::lockout_interval', 1);

        $lockoutInterval  = (new Carbon('now'))->addMinutes($lockout);

        $throttleInterval = (new Carbon('now'))->addMinutes(
            $settings->value('anomaly.extension.throttle_security_check::throttle_interval', 1)
        );

        $key = $extension->key($request);

        if ($expiration = $cache->get($extension->getNamespace('expiration:' . $key))) {

            $authenticator->logout(); // Just for safe measure.

            return dispatch_sync(new MakeResponse($lockout * 60 - (time() - $expiration)));
        }

        $attempts = $cache->get($extension->getNamespace('attempts:' . $key), 0) + 1;

        $cache->put($extension->getNamespace('attempts:' . $key), $attempts, $throttleInterval);

        if ($attempts > $maxAttempts) {

            $cache->put($extension->getNamespace('expiration:' . $key), time(), $lockoutInterval);

            $authenticator->logout(); // Just for safe measure.

            return dispatch_sync(new MakeResponse($lockout * 60));
        }

        return true;
    }
}
