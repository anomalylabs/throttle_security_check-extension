<?php namespace Anomaly\ThrottleSecurityCheckExtension\Command;

use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
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
     * @param SettingRepositoryInterface $settings
     * @param ResponseFactory            $response
     * @param Factory                    $view
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(SettingRepositoryInterface $settings, ResponseFactory $response, Factory $view)
    {
        $message         = $settings->value('anomaly.extension.throttle_security_check::error_message');
        $lockoutInterval = $settings->value('anomaly.extension.throttle_security_check::lockout_interval', 1);

        return $response->make($view->make('streams::errors/429', compact('message')), 429)
            ->setTtl($lockoutInterval * 1);
    }
}
