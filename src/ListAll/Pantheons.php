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

class Pantheons extends Base
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        //$response->getBody()->write(print_r($this->pantheonList, true));
        $output = [];
        foreach ($this->pantheonList as $pantheonName => $pantheon) {
            $output[$pantheonName] = $pantheon;
        }
        $response->getBody()->write(json_encode($output, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
