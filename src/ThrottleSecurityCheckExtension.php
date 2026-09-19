<?php namespace Anomaly\ThrottleSecurityCheckExtension;

use Anomaly\ThrottleSecurityCheckExtension\Command\ThrottleLogin;
use Anomaly\UsersModule\User\Contract\UserInterface;
use Anomaly\UsersModule\User\Security\SecurityCheckExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThrottleSecurityCheckExtension
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class ThrottleSecurityCheckExtension extends SecurityCheckExtension
{

    /**
     * This extension provides a security check
     * for users that assures the user is not throttle.
     *
     * @var string
     */
    protected $provides = 'anomaly.module.users::security_check.throttle';

    /**
     * Check a login attempt.
     *
     * @return bool|Response
     */
    public function attempt()
    {
        return dispatch_sync(new ThrottleLogin());
    }

    /**
     * Check an HTTP request.
     *
     * @param  UserInterface $user
     * @return bool|Response
     */
    public function check(?UserInterface $user = null)
    {
        return true;
    }

    /**
     * Return the throttle key for an attempt.
     *
     * @param  Request $request
     * @return string
     */
    public function key(Request $request)
    {
        $identifier = $request->input(
            config('anomaly.module.users::config.login', 'email')
        );

        if (!is_scalar($identifier)) {
            $identifier = '';
        }

        return sha1(Str::lower(trim((string)$identifier)) . '|' . $request->ip());
    }
}
