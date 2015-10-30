<?php namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Anomaly\ThrottleSecurityCheckExtension\ThrottleSecurityCheckExtension;
use Anomaly\UsersModule\User\UserAuthenticator;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

/**
 * Class ThrottleLogin
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ThrottleSecurityCheckExtension\Command
 */
class ThrottleLogin implements SelfHandling
{

    use DispatchesJobs;

    /**
     * Handle the command.
     *
     * @param Repository                     $cache
     * @param Request                        $request
     * @param UserAuthenticator              $authenticator
     * @param ThrottleSecurityCheckExtension $extension
     * @return bool
     */
    public function handle(
        Repository $cache,
        Request $request,
        UserAuthenticator $authenticator,
        ThrottleSecurityCheckExtension $extension
    ) {

        $attempts   = $cache->get($extension->getNamespace('attempts:' . $request->ip()), 1);
        $expiration = $cache->get($extension->getNamespace('expiration:' . $request->ip()));

        if ($expiration || $attempts > 5) {

            $cache->put($extension->getNamespace('attempts:' . $request->ip()), $attempts + 1, 1);
            $cache->put($extension->getNamespace('expiration:' . $request->ip()), time() + 60, 1);

            $authenticator->logout(); // Just in case.

            return $this->dispatch(new MakeResponse());
        }

        $cache->put($extension->getNamespace('attempts:' . $request->ip()), $attempts + 1, 1);

        return true;
    }

}
