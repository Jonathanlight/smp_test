<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $route = $request->attributes->get('_route');

        $routeOrder = [
            'order_step1',
            'order_step2',
            'order_step3',
            'order_callback',
        ];

        if (!in_array($route, $routeOrder)) {
            // $request->getSession()->remove('order');
        }
    }
}
