<?php

namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Anomaly\ThrottleSecurityCheckExtension\ThrottleSecurityCheckExtension;
use Anomaly\UsersModule\User\UserAuthenticator;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
     * @param  Configuration                  $configuration
     * @return bool
     */
    public function handle(
        Repository $cache,
        Request $request,
        UserAuthenticator $authenticator,
        SettingRepositoryInterface $settings,
        ThrottleSecurityCheckExtension $extension,
        Configuration $configuration
    ) {
        $maxAttempts = $settings->value('anomaly.extension.throttle_security_check::max_attempts', 5);

        $lockoutInterval  = (new Carbon('now'))->addMinutes(
            $settings->value('anomaly.extension.throttle_security_check::lockout_interval', 1)
        );

        $throttleInterval = (new Carbon('now'))->addMinutes(
            $settings->value('anomaly.extension.throttle_security_check::throttle_interval', 1)
        );

        $key = $this->key($request, $configuration);

        if ($cache->get($extension->getNamespace('expiration:' . $key))) {

            $authenticator->logout(); // Just for safe measure.

            return dispatch_sync(new MakeResponse());
        }

        $attempts = $cache->get($extension->getNamespace('attempts:' . $key), 1);

        $cache->put($extension->getNamespace('attempts:' . $key), $attempts + 1, $throttleInterval);

        if ($attempts >= $maxAttempts) {

            $cache->put($extension->getNamespace('expiration:' . $key), time(), $lockoutInterval);

            $authenticator->logout(); // Just for safe measure.

            return dispatch_sync(new MakeResponse());
        }

        return true;
    }

    /**
     * Return the cache key for the attempt.
     *
     * @param  Request       $request
     * @param  Configuration $configuration
     * @return string
     */
    protected function key(Request $request, Configuration $configuration)
    {
        $identifier = $request->input(
            $configuration->get('anomaly.module.users::config.login', 'email')
        );

        if (!is_scalar($identifier)) {
            $identifier = '';
        }

        return sha1(Str::lower(trim((string)$identifier)) . '|' . $request->ip());
    }
}
