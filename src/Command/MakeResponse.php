<?php namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;

/**
 * Class MakeResponse
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ThrottleSecurityCheckExtension\Command
 */
class MakeResponse implements SelfHandling
{

    /**
     * Handle the command.
     *
     * @param ResponseFactory $response
     * @param Factory         $view
     */
    public function handle(ResponseFactory $response, Factory $view)
    {
        return $response->make($view->make('streams::errors/429'), 429);
    }
}
