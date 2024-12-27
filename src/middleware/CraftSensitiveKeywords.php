<?php

namespace webrgp\ignition\middleware;

use Craft;
use Spatie\FlareClient\Report;

class CraftSensitiveKeywords
{
    public function handle(Report $report, $next)
    {
        $context = $report->allContext();

        if (Craft::$app && isset($context['request_data']['body'])) {
            $security = Craft::$app->getSecurity();
            $bodyParams = $context['request_data']['body'];
            foreach ($bodyParams as $key => $value) {
                $context['request_data']['body'][$key] = $security->isSensitive($key) ? '<CENSORED>' : $value;
            }
        }

        $report->userProvidedContext($context);

        return $next($report);
    }
}
