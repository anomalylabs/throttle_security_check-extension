<?php namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Anomaly\ThrottleSecurityCheckExtension\ThrottleSecurityCheckExtension;
use Anomaly\UsersModule\User\UserAuthenticator;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThrottleRequest
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ThrottleSecurityCheckExtension\Command
 */
class ThrottleRequest implements SelfHandling
{

    use DispatchesJobs;

    /**
     * Handle the command.
     *
     * @param Repository                     $cache
     * @param Request                        $request
     * @param UserAuthenticator              $authenticator
     * @param ThrottleSecurityCheckExtension $extension
     * @return bool|Response
     */
    public function handle(
        Repository $cache,
        Request $request,
        UserAuthenticator $authenticator,
        ThrottleSecurityCheckExtension $extension
    ) {
        $key = md5($request->ip() . $request->fullUrl() . json_encode($request->all()));

        $attempts   = $cache->get($extension->getNamespace('attempts:' . $key), 1);
        $expiration = $cache->get($extension->getNamespace('expiration:' . $key));

        if ($expiration || $attempts > 60) {

            $cache->put($extension->getNamespace('attempts:' . $key), $attempts + 1, 1);
            $cache->put($extension->getNamespace('expiration:' . $key), time() + 60, 1);

            $authenticator->logout();

            return $this->dispatch(new MakeResponse());
        }

        $cache->put($extension->getNamespace('attempts:' . $key), $attempts + 1, 1);

        return true;
    }

}
