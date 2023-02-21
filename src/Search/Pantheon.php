<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pantheon extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $pantheon = strtolower($request->getAttribute('pantheon'));
        foreach ($this->pantheonList as $pantheonName => $pantheonFound) {
            if (stristr($pantheonName, $pantheon)) {
                $this->matchedDeities[$pantheonName] = $pantheonFound;
            }
        }

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
