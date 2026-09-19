<?php namespace Anomaly\ThrottleSecurityCheckExtension\Listener;

use Anomaly\ThrottleSecurityCheckExtension\ThrottleSecurityCheckExtension;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;

/**
 * Class ClearThrottle
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 */
class ClearThrottle
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
     * The extension instance.
     *
     * @var ThrottleSecurityCheckExtension
     */
    protected $extension;

    /**
     * Create a new ClearThrottle instance.
     *
     * @param Repository                     $cache
     * @param Request                        $request
     * @param ThrottleSecurityCheckExtension $extension
     */
    public function __construct(
        Repository $cache,
        Request $request,
        ThrottleSecurityCheckExtension $extension
    ) {
        $this->cache     = $cache;
        $this->request   = $request;
        $this->extension = $extension;
    }

    /**
     * Handle the event.
     */
    public function handle()
    {
        $key = $this->extension->key($this->request);

        $this->cache->forget($this->extension->getNamespace('attempts:' . $key));
        $this->cache->forget($this->extension->getNamespace('expiration:' . $key));
    }
}
