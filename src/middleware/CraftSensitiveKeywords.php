<?php

namespace webrgp\ignition\middleware;

use Closure;
use Craft;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;

class CraftSensitiveKeywords implements FlareMiddleware
{
    public function handle(Report $report, Closure $next)
    {
        $context = $report->allContext();

        if (isset($context['request_data']['body'])) {
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
