<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MatchAllPurview extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $purviewArray = explode('/', strtolower($request->getAttribute('purviews')));
        sort($purviewArray);
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            foreach ($pantheon as $deityName => $deity) {
                $arrayFound = [];
                foreach ($deity->purviews as $purview) {
                    foreach ($purviewArray as $queryPurview) {
                        if (stristr($purview, $queryPurview)) {
                            $arrayFound[] = $purview;
                        }
                    }
                }

                if (count($arrayFound) === count($purviewArray)) {
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
