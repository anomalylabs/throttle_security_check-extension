<?php namespace Anomaly\ThrottleSecurityCheckExtension;

use Anomaly\UsersModule\Security\SecurityCheckExtension;

/**
 * Class ThrottleSecurityCheckExtension
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Addon\Extension\ThrottleSecurityCheckExtension
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
     * This is a login type
     * security check.
     *
     * @var string
     */
    protected $type = 'login';

}
