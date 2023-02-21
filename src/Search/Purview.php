<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Purview extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $purview = str_replace('+', ' ', strtolower($request->getAttribute('purview')));
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            $this->matchedDeities[$pantheonName] = [];
            foreach ($pantheon as $deityName => $deity) {
                foreach ($deity->purviews as $purviewFound) {
                    if (stristr($purviewFound, $purview)) {
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
