<?php

namespace ScionAPI;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractRoute
 * @package ScionAPI
 */
abstract class AbstractRoute
{
    /**
     * @var array
     */
    protected $help = [];

    /**
     * @return object
     */
    public function getHelp()
    {
        return (object)$this->help;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    abstract public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface;

    /**
     * @param Response $response
     * @param array $outArray
     * @return Response
     */
    protected function outputResponse(Response $response, array $outArray) : Response
    {
        $response->getBody()->write(json_encode($outArray, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
