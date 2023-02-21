<?php

namespace ScionAPI\CRUD\Pantheon\Add;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScionAPI\Generic\Record;
use ScionAPI\Search\Base;

class Purview extends Base
{
    /**
     * @inheritDoc
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $data = $request->getParsedBody();

        // @TODO: Replace with real security.
        if ($data['password'] === '0dh1nn') {
            /** @var Record $pantheonRecord */
            $pantheonRecord = $this->pantheonList->getRecordByKey($data['pantheon']);

            /**
             * @var string $deityName
             * @var Record $deity
             */
            foreach ($pantheonRecord as $deityName => $deity) {
                $tempPurviews = [];
                foreach ($deity->purviews as $index => $purview) {
                    $tempPurviews[] = $purview;
                }
                $tempPurviews[] = $data['purview'];
                $deity->purviews->set($tempPurviews);

                $this->pantheonList->addRecord($pantheonRecord, $data['pantheon'], true);
                $this->pantheonList->save();
            }

            $response->getBody()->write(json_encode($pantheonRecord, JSON_PRETTY_PRINT));
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
