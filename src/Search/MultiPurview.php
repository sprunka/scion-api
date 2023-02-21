<?php
/**
 * Created by PhpStorm.
 * User: sprunka * Date: 2020-10-02
 * Time: 22:07
 */

namespace ScionAPI\Search;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MultiPurview extends Base
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

                sort($arrayFound);
                if (count($arrayFound) === count($purviewArray)) {
                    $this->matchedDeities[$pantheonName]['matched_all'][$deityName] = $arrayFound;
                }
                if (count($arrayFound) !== 0 && count($arrayFound) !== count($purviewArray)) {
                    $this->matchedDeities[$pantheonName]['partial_match'][$deityName] = $arrayFound;
                }
            }
            if (key_exists('matched_all', $this->matchedDeities[$pantheonName])) {
                ksort($this->matchedDeities[$pantheonName]['matched_all']);
            }
            if (key_exists('partial_match', $this->matchedDeities[$pantheonName])) {
                ksort($this->matchedDeities[$pantheonName]['partial_match']);
            }
            ksort($this->matchedDeities[$pantheonName]);
        }
        ksort($this->matchedDeities);

        $response->getBody()->write(json_encode($this->matchedDeities, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
