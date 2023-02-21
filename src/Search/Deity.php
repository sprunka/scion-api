<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Deity extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $deity = strtolower($request->getAttribute('deity'));
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            foreach ($pantheon as $deityName => $deityFound) {
                if (stristr($deityName, $deity)) {
                    $this->matchedDeities[$pantheonName][$deityName] = $deityFound;
                    break;
                }
            }
        }

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
