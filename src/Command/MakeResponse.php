<?php namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;

/**
 * Class MakeResponse
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class MakeResponse
{

    /**
     * The seconds left on the lockout.
     *
     * @var int
     */
    protected $retryAfter;

    /**
     * Create a new MakeResponse instance.
     *
     * @param int $retryAfter
     */
    public function __construct($retryAfter = 0)
    {
        $this->retryAfter = $retryAfter;
    }

    /**
     * Handle the command.
     *
     * @param  ResponseFactory                            $response
     * @param  Factory                                    $view
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(ResponseFactory $response, Factory $view)
    {
        return $response->make($view->make('streams::errors/429', []), 429)
            ->header('Retry-After', max((int)$this->retryAfter, 1));
    }
}
