<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScionAPI\Choose\PersonName;
use ScionAPI\CRUD\Pantheon\Add as AddPantheon;
use ScionAPI\CRUD\Pantheon\Add\Deity as AddDeity;
use ScionAPI\CRUD\Pantheon\Add\Purview as AddPurview;
use ScionAPI\ListAll\Deities as ListDeities;
use ScionAPI\ListAll\Pantheons as ListPantheons;
use ScionAPI\Search\Calling;
use ScionAPI\Search\Deity;
use ScionAPI\Search\MatchAllCalling;
use ScionAPI\Search\MatchAllPurview;
use ScionAPI\Search\MultiCalling;
use ScionAPI\Search\MultiPurview;
use ScionAPI\Search\Pantheon;
use ScionAPI\Search\Purview;
use Slim\App;
use

use function OpenApi\scan;return function (App $app) {
    $app->get('/', function (
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $response->getBody()->write(json_encode(["Nothing"=>"to see here."]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });
    // Name Generator
    $app->get('/name[/{gender}[/{firstLastFull}]]', PersonName::class)->setName('generateName');

    // GET Lists
    $app->get('/list/deities/{pantheon}', ListDeities::class)->setName('listDeity');
    $app->get('/list/pantheons', ListPantheons::class)->setName('listPantheon');

    // GET Searches
    $app->get('/search/calling/{calling}', Calling::class)->setName('searchCalling');
    $app->get('/search/callings/{callings:.*}', MultiCalling::class)->setName('multiSearchCalling');
    $app->get('/match/all/callings/{callings:.*}', MatchAllCalling::class)->setName('strictMatchSearchCalling');
    $app->get('/search/purview/{purview}', Purview::class)->setName('searchPurview');
    $app->get('/search/purviews/{purviews:.*}', MultiPurview::class)->setName('multiSearchPurview');
    $app->get('/match/all/purviews/{purviews:.*}', MatchAllPurview::class)->setName('strictMatchSearchPurview');
    $app->get('/search/deity/{deity}', Deity::class)->setName('searchDeity');
    $app->get('/search/pantheon/{pantheon}', Pantheon::class)->setName('searchPantheon');

    // PUT/POST Updates
    $app->post('/update/pantheon/add/purview', AddPurview::class)->setName('addPurview');
    $app->put('/update/pantheon/add/purview', AddPurview::class)->setName('addPurview');
    $app->post('/update/pantheon/add/deity', AddDeity::class)->setName('addDeity');
    $app->put('/update/pantheon/add/deity', AddDeity::class)->setName('addDeity');
    $app->post('/update/pantheon/add', AddPantheon::class)->setName('addPantheon');
    $app->put('/update/pantheon/add', AddPantheon::class)->setName('addPantheon');

    //Swagger Docs
    $app->get('/docs', \App\Action\Docs\SwaggerUiAction::class);

    $app->get('/openapi', function ($request, $response, $args) {
        $swagger = scan('--PATH TO PROJECT ROOT--');
        $response->getBody()->write(json_encode($swagger));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
