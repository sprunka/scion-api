<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MatchAllCalling extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $callingArray = explode('/', strtolower($request->getAttribute('callings')));
        sort($callingArray);
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            foreach ($pantheon as $deityName => $deity) {
                $deityCallings = [];
                foreach ($deity->callings as $calling) {
                    $deityCallings[] = strtolower($calling);
                }
                sort($deityCallings);
                $arrayFound = array_intersect($callingArray, $deityCallings);
                sort($arrayFound);

                if (count($arrayFound) === count($callingArray)) {
                    $this->matchedDeities[$pantheonName][] = $deityName;
                    sort($this->matchedDeities[$pantheonName]);
                }
            }
        }
        ksort($this->matchedDeities);

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
