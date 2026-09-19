<?php namespace Anomaly\ThrottleSecurityCheckExtension;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\ThrottleSecurityCheckExtension\Listener\ClearThrottle;
use Anomaly\UsersModule\User\Event\UserWasLoggedIn;

/**
 * Class ThrottleSecurityCheckExtensionServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 */
class ThrottleSecurityCheckExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * The addon listeners.
     *
     * @var array
     */
    protected $listeners = [
        UserWasLoggedIn::class => [
            ClearThrottle::class,
        ],
    ];
}
