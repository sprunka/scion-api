<?php

namespace ScionAPI;

use Exception;
use ScionAPI\AbstractRoute as Route;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

/**
 * Class Help
 * @package ScionAPI\Roll
 */
class Help extends Route
{
    protected $help = [];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response (JSON Encoded content)
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response)
    {
        $path = $request->getRequestTarget();
        $what = $request->getAttribute('what');

        $pathSegments = explode('/', $path);
        array_pop($pathSegments);
        $whatSegments = explode('/', $what);

        // To get help for a specific route, analyze the path and the "what" segments
        // TODO: Write help docs for routes and methods.
        // For now, just return generic help.

        $jsonResponse = $response->withJson($this->getHelp(), 200);
        return $jsonResponse;
    }

    public function getHelp()
    {
        $this->help['Description'] = <<<EOH
General help document. Not very helpful. Try: /help/roll or /roll/help
EOH;
        return parent::getHelp();
    }
}
