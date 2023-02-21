<?php

namespace ScionAPI\Choose;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ScionAPI\AbstractRoute;

class CharacterVoice extends AbstractRoute
{

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $labanOrAll = strtolower($request->getAttribute('laban'));
        $laban = false;
        if ($labanOrAll === 'yes' || $labanOrAll == '1' || $labanOrAll === 'true' || $labanOrAll === 'laban' || $labanOrAll === 'on') {
            $laban = true;
        }

        // Base Voice Combos
        $weight = ['No Weight', 'Heavy', 'Light'];
        $spatial = ['Neutral Space', 'Direct', 'Indirect'];
        $timing = ['No Timing', 'Sudden', 'Sustained'];
        $all = [];
        if ($laban === true) {
            $all = [
                'Dabbing - Light, Direct, Sudden',
'Flicking - Light, Indirect, Sudden',
'Pressing - Heavy, Direct, Sustained',
'Floating - Light, Indirect, Sustained',
'Thrusting - Heavy, Indirect, Sudden',
'Wringing - Heavy, Indirect, Sustained',
'Slashing -  Heavy, Direct, Sudden',
'Gliding - Light, Direct, Sustained',
            ];
        }

        // Add-Ons:
        $addOns = [
            'Air Source' => ['Throaty', 'Nasal', false],
            'Air Variant' => ['Breathy', 'Dry', false],
            'Age Variant' => ['Child', false, 'Old'],
            'Gender Inclination' => ['Masc', 'Femme', false],
            'Body Size' => ['Small', false, 'Large'],
            'Tempo' => ['Slow', false, 'Fast'],
            'Tone' => ['Friendly', false, 'Aggressive'],
            'Impairments' => ['Strong', 'Mild', false]
        ];

        $addOnsChosen = [];
        foreach ($addOns as $key => $addOn) {
            $pick = rand(0,2);
            if ($addOn[$pick] !== false) {
                $addOnsChosen[] = [$key => $addOn[$pick]];
            }
        }

        foreach ($weight as $k1 => $w) {
            foreach ($spatial as $k2 => $s) {
                foreach ($timing as $k3 => $t){
                    if ($laban === false) {
                        $all[] = $w . ', ' . $s . ', ' . $t;
                    }
                }
            }
        }

        $outputJSON = [
            'base_voice' => $all[rand(0,count($all)-1)],
            'add_ons' => $addOnsChosen,
        ];

        $response->getBody()->write(json_encode($outputJSON, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
