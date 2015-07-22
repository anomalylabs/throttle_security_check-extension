<?php namespace Anomaly\ThrottleSecurityCheckExtension;

use Anomaly\UsersModule\Authenticator\Authenticator;
use Anomaly\UsersModule\User\Contract\UserInterface;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;

/**
 * Class ThrottleSecurityCheckHandler
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ThrottleSecurityCheckExtension
 */
class ThrottleSecurityCheckHandler
{

    /**
     * The cache repository.
     *
     * @var Repository
     */
    protected $cache;

    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The throttle extension instance.
     *
     * @var ThrottleSecurityCheckExtension
     */
    protected $extension;

    /**
     * The authenticator utility.
     *
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * Create a new ActivationSecurityCheckHandler instance.
     *
     * @param Repository                     $cache
     * @param Request                        $request
     * @param Authenticator                  $authenticator
     * @param ThrottleSecurityCheckExtension $extension
     */
    public function __construct(
        Repository $cache,
        Request $request,
        Authenticator $authenticator,
        ThrottleSecurityCheckExtension $extension
    ) {
        $this->cache         = $cache;
        $this->request       = $request;
        $this->extension     = $extension;
        $this->authenticator = $authenticator;
    }

    /**
     * Handle the security check.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function handle(UserInterface $user = null)
    {
        /**
         * If the user is present
         * or this is not a post request
         * then we don't need to throttle.
         */
        if ($user || $this->request->method() !== 'POST') {
            return true;
        }

        $attempts   = $this->getLoginAttempts();
        $expiration = $this->getExpirationTime();

        if ($expiration || $attempts > 5) {

            $this->setLoginAttempts($attempts + 1);

            return false;
        }

        $this->setLoginAttempts($attempts + 1);

        return true;
    }

    /**
     * Get the login attempts.
     *
     * @return int
     */
    protected function getLoginAttempts()
    {
        return $this->cache->get($this->extension->getNamespace('attempts:' . $this->request->ip()), 0);
    }

    /**
     * Set the login attempts.
     *
     * @param $attempts
     */
    protected function setLoginAttempts($attempts)
    {
        $this->cache->put($this->extension->getNamespace('attempts:' . $this->request->ip()), $attempts, 1);
    }

    /**
     * Get the expiration time.
     *
     * @return null|string
     */
    protected function getExpirationTime()
    {
        return $this->cache->get($this->extension->getNamespace('expiration:' . $this->request->ip()));
    }

    /**
     * Set the expiration time.
     */
    public function setExpirationTime()
    {
        $this->cache->put($this->extension->getNamespace('expiration:' . $this->request->ip()), time() + 60, 1);
    }
}
