<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Calling extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $calling = strtolower($request->getAttribute('calling'));
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            $this->matchedDeities[$pantheonName] = [];
            foreach ($pantheon as $deityName => $deity) {
                foreach ($deity->callings as $callingFound) {
                    if (stristr($callingFound, $calling)) {
                        $this->matchedDeities[$pantheonName][] = $deityName;
                        break;
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
