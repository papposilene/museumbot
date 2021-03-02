<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use Abraham\TwitterOAuth\TwitterOAuth;
use MusicBrainz\Value\Length;

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$dotenv->required([
    'TWITTER_PATCHEDARTWORKBOT_CONSUMERKEY',
    'TWITTER_PATCHEDARTWORKBOT_CONSUMERSECRET',
    'TWITTER_PATCHEDARTWORKBOT_TOKEN',
    'TWITTER_PATCHEDARTWORKBOT_TOKENSECRET',
])->notEmpty();

$client = new Client([
    'headers' => [
        'User-Agent' => 'papposilene-museumbot/1.0',
    ]
]);

/*
 * Metropolitan Museum of Art
 * Find the total of available artworks from MET api
 * Search for artwork with oil (on canvas) in medium
 */
$metApi = 'https://collectionapi.metmuseum.org/public/collection/v1/';
$objectsApi = $client->request('GET', $metApi . 'search?hasImages=true&medium=Oil&q=painting')->getBody();
$objectIds = json_decode($objectsApi, true);
$objectId = array_rand($objectIds['objectIDs']);

// Find an artwork between 1 and the total of available artworks
$objectApi = $client->request('GET', $metApi . 'objects/' . $objectIds['objectIDs'][$objectId])->getBody();
$objectData = json_decode($objectApi, true);

$imgFile = substr($objectData['primaryImageSmall'], strrpos($objectData['primaryImageSmall'], '/') + 1);
$imgData = file_get_contents($objectData['primaryImageSmall']);
$imgPath = __DIR__.'/../storage/patchedartworkbot-img/' . $imgFile;

try {
    file_put_contents($imgPath, $imgData);
}
catch (Exception $e) {
    die('Image: no data for the artwork');
}

/*
 * Changelogs
 */
$patchFile = __DIR__.'/../storage/patchedartworkbot-data/changelogs.csv';
$patchCsv = array_map('str_getcsv', file($patchFile));
array_walk($patchCsv, function(&$a) use ($patchCsv) {
    $a = array_combine($patchCsv[0], $a);
});
array_shift($patchCsv);
$patchLog = array_rand($patchCsv);

/*
 * Create a tweet, then post it to Twitter
 */
// TODO: mb_strimwidth("Hello World", 0, 10, "...");
$tweet = $objectData['title'] . ' by ' . $objectData['artistDisplayName'] . ', but in which ' . $patchCsv[$patchLog]['changelog'];
if(strlen($tweet) > 280) {
    $title = mb_strimwidth($objectData['title'], 0, 20, '...');
    $tweet = $title . ' by ' . $objectData['artistDisplayName'] . ', but in which ' . $patchCsv[$patchLog]['changelog'];
}

// Twitter API: set up the connection
try {
    $twitter = new TwitterOAuth(
        $_ENV['TWITTER_PATCHEDARTWORKBOT_CONSUMERKEY'],
        $_ENV['TWITTER_PATCHEDARTWORKBOT_CONSUMERSECRET'],
        $_ENV['TWITTER_PATCHEDARTWORKBOT_TOKEN'],
        $_ENV['TWITTER_PATCHEDARTWORKBOT_TOKENSECRET']
    );
}
catch (Exception $e) {
    die('Twitter: error during the connection.');
}

// Twitter API: upload the artwork for the tweet
try {
    $uploaded_media = $twitter->upload('media/upload', ['media' => $imgPath]);
}
catch (Exception $e) {
    die('Twitter: error during uploading the media.');
}

// Twitter API: post the tweet with the uploaded artwort
try {
    $twitter->post('statuses/update', [
        'status' => $tweet,
        'media_ids' => $uploaded_media->media_id_string,
    ]);
}
catch (Exception $e) {
	die('Twitter: error during tweeting.');
}
// Delete the stored image
unlink($imgPath);
