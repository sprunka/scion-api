<?php

namespace ScionAPI\CRUD\Pantheon\Add;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScionAPI\Generic\Record;
use ScionAPI\Search\Base;

class Deity extends Base
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
            $deityToAdd = $data['deity']['name'];
            $callingsToAdd = $data['deity']['callings'];
            $purviewsToAdd = $data['deity']['purviews'];

            // @TODO: FIX THIS! with DI
            //$deityRecord = new Record(['callings' => $callingsToAdd, 'purviews' => $purviewsToAdd]);
            $deityRecord = $this->deity;
            $deityRecord->set(['callings' => $callingsToAdd, 'purviews' => $purviewsToAdd]);


            /** @var Record $pantheonRecord */
            $pantheonRecord = $this->pantheonList->getRecordByKey($data['pantheon']);
            if ($pantheonRecord === null) {
                $pantheonRecord = $this->pantheon;
            }

            $tempDeities = [];
            /**
             * @var string $deityName
             * @var Record $deity
             */
            foreach ($pantheonRecord as $deityName => $deity) {
                $tempDeities[$deityName] = $deity;
            }
            $tempDeities[$deityToAdd] = $deityRecord;
            $pantheonRecord->set($tempDeities);

            $this->pantheonList->addRecord($pantheonRecord, $data['pantheon'], true);
            $this->pantheonList->save();
            $response->getBody()->write(json_encode($pantheonRecord, JSON_PRETTY_PRINT));
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
