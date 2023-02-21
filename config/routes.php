<?php

/**
 * @OA\Info(
 *   title="OpenAPI Docs for VampyreBytes's Scion API. -- Compatible with Scion Second Edition",
 *   description="This product was created under license. STORYPATH SYSTEM, STORYPATH NEXUS COMMUNITY CONTENT PROGRAM,
and all related game line terms and logos are trademarks of Onyx Path Publishing. All setting material, art, and
trade dress are the property of Onyx Path Publishing. www.theonyxpath.com  This work contains material that is copyright
Onyx Path Publishing. Such material is used with permission under the Community Content Agreement for Storypath Nexus
Community Content Program. All other original material in this work is copyright 2022 by Sean Prunka and published under
the Community Content Agreement for Storypath Nexus Community Content Program.
Requires the use of Scion: Origin from Onyx Path Publishing. Also features content from Hero, Demigod, and Titanomachy.
((Dragon and Mythos are still pending Final Copy.))",
 *   version="1.0.0",
 *   @OA\Contact(
 *     name="Vampyre Bytes",
 *     email="admin@vampyrebytes.com"
 *   )
 * )
 *
 * @OA\Server(
 *     url="https://scion.vampyrebytes.com"
 * )
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScionAPI\Choose\CharacterVoice;
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

return function (App $app) {
    $app->get('/', function (
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $response->getBody()->write(json_encode(["Refer to the documentation at /openapi"], JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    /**
     * @OA\Get(
     *     path="/openapi.json",
     *     summary="OpenAPI 3 documentation",
     *     @OA\Response(
     *         response="200",
     *         description="OpenAPI 3 documentation"
     *     )
     * )
     */
    $app->get('/openapi.json', function ($request, $response, $args) {
        $response->getBody()->write(json_encode(OpenApi\Generator::scan([__DIR__]), JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });
    $app->get('/openapi', function ($request, $response, $args) {
        $response->getBody()->write(json_encode(OpenApi\Generator::scan([__DIR__]), JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Name Generator
    /**
     * @OA\Get(
     *     path="/name/{gender}/{firstLastFull}",
     *     @OA\Parameter(
     *         name="gender",
     *         in="path",
     *         required=true,
     *         description="Gender: male, female, or neutral.",
     *         @OA\Schema(
     *             type="string",
     *             nullable=true,
     *             oneOf={
     *                 @OA\Schema(type="string", enum={"male", "female"}),
     *                 @OA\Schema(type="string", enum={"neutral"}, nullable=true)
     *             }
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="firstLastFull",
     *         in="path",
     *         required=true,
     *         description="Name part: first, last, or full.",
     *         @OA\Schema(
     *             type="string",
     *             nullable=true,
     *             oneOf={
     *                 @OA\Schema(type="string", enum={"first", "last", "full"}),
     *                 @OA\Schema(type="null")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Generates a random 'normal' name."
     *     )
     * )
     */
    $app->get('/name[/{gender}[/{firstLastFull}]]', PersonName::class)->setName('generateName');

    // Voice Generator
    /**
     * @OA\Get(
     *     path="/voice/{laban}",
     *     @OA\Parameter(
     *       name="laban",
     *       in="path",
     *       schema={
     *	       "type"="boolean",
     *         "nullable"=false,
     *         "enum"={true, false}
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description=">
    Laban:
     * `true` - Generates only Laban base voice types. (But includes the main Laban Type descriptors. However, it may also include subtypes and/or quirks.)
     * `false` - Generates a charcater voice, based on the Laban variants, but includes all variants, not only teh Laban-specific variations. Also may include one or more subtypes and/or quirks"
     *     ),
     *     @OA\Response(response="200", description="Generates a random Voice Pattern (add 'laban' to limit the base voice pattern to a Laban style).")
     * )
     */
    $app->get('/voice[/{laban}]', CharacterVoice::class)->setName('generateVoice');

    // GET Lists
    /**
     * @OA\Get(
     *     path="/list/deities/{pantheon}",
     *     @OA\Parameter(
     *       name="pantheon",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=false
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="Pantheon to be shown. Exact spelling is required, using latin characters (no diacriticals). Case-insensitive. Try the search if you want to use a partial name."
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities within the given {pantheon}."
     *     )
     * )
     */
    $app->get('/list/deities/{pantheon}', ListDeities::class)->setName('listDeity');

    /**
     * @OA\Get(
     *     path="/list/pantheons",
     *     @OA\Response(
     *         response="200",
     *         description="Gets a list of everything currently recorded.."
     *     )
     * )
     */
    $app->get('/list/pantheons', ListPantheons::class)->setName('listPantheon');

    // GET Searches
    /**
     * @OA\Get(
     *     path="/search/calling/{calling}",
     *     @OA\Parameter(
     *       name="calling",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=false
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="This string will be used to search the database for dieties with access to the Calling. (Partials allowed, but not clarified.)"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities with access to {calling}. Listed by Pantheon."
     *     )
     * )
     */
    $app->get('/search/calling/{calling}', Calling::class)->setName('searchCalling');

    //MultiSearch works, but I don't understand how to Document it.
    $app->get('/search/callings/{callings:.*}', MultiCalling::class)->setName('multiSearchCalling');
    /**
     * @OA\Get(
     *     path="/match/all/callings/{callings}",
     *     @OA\Parameter(
     *         name="callings",
     *         in="path",
     *         required=true,
     *         description="A forward slash '/' separated list of callings to search for (e.g. death, fertility)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities with access to all of the given callings. Listed by Pantheon."
     *     )
     * )
     */
    $app->get('/match/all/callings/{callings:.*}', MatchAllCalling::class)->setName('strictMatchSearchCalling');

    /**
     * @OA\Get(
     *     path="/search/purview/{purview}",
     *     @OA\Parameter(
     *       name="purview",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=false
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="This string will be used to search the database for dieties with access to the Purview. (Partials allowed, but not clarified.)"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities with access to {purview}. Listed by Pantheon."
     *     )
     * )
     */
    $app->get('/search/purview/{purview}', Purview::class)->setName('searchPurview');

    //MultiSearch works, but I don't understand how to Document it.
    $app->get('/search/purviews/{purviews:.*}', MultiPurview::class)->setName('multiSearchPurview');
    $app->get('/match/all/purviews/{purviews:.*}', MatchAllPurview::class)->setName('strictMatchSearchPurview');

    /**
     * @OA\Get(
     *     path="/search/deity/{deity}",
     *     @OA\Parameter(
     *       name="deity",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=false
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="This string will be used to search the database for dieties with this name. (Partials allowed.)"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities with {deity} in their name. Listed by Pantheon."
     *     )
     * )
     */
    $app->get('/search/deity/{deity}', Deity::class)->setName('searchDeity');

    /**
     * @OA\Get(
     *     path="/search/pantheon/{pantheon}",
     *     @OA\Parameter(
     *       name="pantheon",
     *       in="path",
     *       schema={
     *	       "type"="string",
     *         "nullable"=false
     *       },
     *       required=true,
     *       allowEmptyValue=false,
     *       description="This string will be used to search the database for dieties in this panth. (Partials allowed.)"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lists all recorded deities in {pantheon}."
     *     )
     * )
     */
    $app->get('/search/pantheon/{pantheon}', Pantheon::class)->setName('searchPantheon');

    // PUT/POST Updates -- There will be no Swagger for this until I lock it down better.
    $app->post('/update/pantheon/add/purview', AddPurview::class)->setName('addPurview');
    $app->put('/update/pantheon/add/purview', AddPurview::class)->setName('addPurview');
    $app->post('/update/pantheon/add/deity', AddDeity::class)->setName('addDeity');
    $app->put('/update/pantheon/add/deity', AddDeity::class)->setName('addDeity');
    $app->post('/update/pantheon/add', AddPantheon::class)->setName('addPantheon');
    $app->put('/update/pantheon/add', AddPantheon::class)->setName('addPantheon');

};
