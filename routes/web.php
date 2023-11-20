<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('draws', function () {

    ini_set('memory_limit', '3000M');
    ini_set('max_execution_time', '0');


    // List names and define initial exclusions
    $players = [
        'tom' => [
            'exclusions' => ['hanna'],
            'draws' => [],
            'displayName' => 'Tom',
            'email' => 'tom@lnch.co.uk',
        ],
        'hanna' => [
            'exclusions' => ['tom'],
            'draws' => [],
            'displayName' => 'Hanna',
            'email' => 'hanna.pickering5693@gmail.com',
        ],
        'daniel' => [
            'exclusions' => ['jade'],
            'draws' => [],
            'displayName' => 'Daniel',
            'email' => 'picked88@hotmail.co.uk',
        ],
        'jade' => [
            'exclusions' => ['daniel'],
            'draws' => [],
            'displayName' => 'Jade',
            'email' => 'jade_heatley@hotmail.com',
        ],
        'jack' => [
            'exclusions' => [],
            'draws' => [],
            'displayName' => 'Jack',
            'email' => 'jackhenningpickering@gmail.com',
        ],
        'susanna' => [
            'exclusions' => ['chris'],
            'draws' => [],
            'displayName' => 'Susanna',
            'email' => 'susanna.krogh@hotmail.co.uk',
        ],
        'chris' => [
            'exclusions' => ['susanna'],
            'draws' => [],
            'displayName' => 'Chris',
            'email' => 'cj.pickering@hotmail.co.uk',
        ],
    ];

    function drawNames($players, $draws = 1) {
        if ($draws > (count($players) - 1)) {
            throw new Exception('There are too many draws for this amount of players');
        }

        for ($i = 1; $i <= $draws; $i++) {
            $players = drawRecipients($players);
        }

        return $players;
    }

    function drawRecipients($players) {
        $attempts = 0;
        $drawIsComplete = false;

        while ($drawIsComplete == false) {
            $playersDrawn = 0;
            $allPlayers = array_keys($players);
            $newPlayers = $players;

            foreach ($players as $player => $playerDetails) {
                // Establish the list of possible recipients (minus themselves, exclusions, and previous draws)
                $excludedRecipients = [
                    $player,
                    ...$playerDetails['exclusions'],
                    ...$playerDetails['draws'],
                ];

                $possibleRecipients = array_filter($allPlayers, function ($player) use ($excludedRecipients) {
                    return ! in_array($player, $excludedRecipients);
                });

                if (! count($possibleRecipients)) {
                    $attempts++;
                    Log::debug('Out of possible options. Attempts: ' . $attempts);
                    break;
                }

                // Pick a random name
                $chosenName = $possibleRecipients[array_rand($possibleRecipients)];

                // Add to the draws array
                $newPlayers[$player]['draws'][] = $chosenName;

                // Remove from the all players list
                unset($allPlayers[array_search($chosenName, $allPlayers)]);

                $playersDrawn++;
            }

            // Check if we successfully completed the loop, if so, we can break here
            if ($playersDrawn == count($players) || $attempts >= 10_000) {
                $drawIsComplete = true;
            }
        }

        // Check if it's valid
        return $newPlayers;
    }

    $drawResults = drawNames($players, 2);

    // Process results by emailing the recipients with their results
    foreach ($drawResults as $player) {
        $notificationEmail = new \App\Mail\SecretSantaNames(player: $player);
        Mail::to($player['email'])->send($notificationEmail);
    }

    dd('Emails should have been sent successfully!');
});