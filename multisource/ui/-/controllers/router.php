<?php namespace ss\multisource\ui\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        $baseRoute = $this->data('route/base');

        \ss\multisource\ui()->setBaseRoute($baseRoute);

        $this->route($baseRoute)->to('~:view');

        $this->route($baseRoute . '/divisions')->to('divisions~:view');
        $this->route($baseRoute . '/divisions/intersections')->to('divisions/intersections~:view');
        $this->route($baseRoute . '/divisions/*')->to('division router:getResponse');

        $this->route($baseRoute . '/mailboxes')->to('mailboxes~:view');
        $this->route($baseRoute . '/mailboxes/*')->to('mailbox router:getResponse');

        return $this->routeResponse();
    }
}
