<?php namespace Anomaly\ThrottleSecurityCheckExtension;

use Illuminate\Routing\ResponseFactory;
use Illuminate\View\Factory;

/**
 * Class ThrottleSecurityCheckResponse
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ThrottleSecurityCheckExtension
 */
class ThrottleSecurityCheckResponse
{

    /**
     * Make the response in the case of a failure.
     *
     * @param ResponseFactory $response
     * @return \Illuminate\Http\Response
     */
    public function make(ResponseFactory $response, Factory $view)
    {
        return $response->make($view->make('streams::errors/429'), 429);
    }
}
