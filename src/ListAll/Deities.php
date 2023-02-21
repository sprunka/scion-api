<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\ListAll;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScionAPI\Search\Base;

class Deities extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $pantheon = strtolower($request->getAttribute('pantheon'));
        foreach ($this->pantheonList as $pantheonName => $pantheonFound) {
            if (strtolower($pantheonName) === $pantheon) {
                $this->matchedDeities[$pantheonName] = $pantheonFound;
            }
        }

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
