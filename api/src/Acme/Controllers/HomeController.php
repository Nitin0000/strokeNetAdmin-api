<?php
namespace Acme\Controllers;

class HomeController extends BaseController
{
    public function __invoke($request, $response, $args)
    {
        $this->ci->logger->info("Slim-Skeleton '/' route");
        $args['_layout'] = '_layout.phtml';
        return $this->ci->renderer->render($response, 'index.phtml', $args);
    }

    // dsdsds
}
