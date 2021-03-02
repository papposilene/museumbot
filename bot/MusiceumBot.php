<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require __DIR__.'/../vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
use MusicBrainz\MusicBrainz;
use MusicBrainz\HttpAdapter\GuzzleHttpAdapter;
use MusicBrainz\Filter\PageFilter;
use MusicBrainz\Filter\Search\ReleaseFilter;
use MusicBrainz\Value\Name;
use GuzzleHttp\Client;

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$dotenv->required([
    'TWITTER_MUSICEUMBOT_CONSUMERKEY',
    'TWITTER_MUSICEUMBOT_CONSUMERSECRET',
    'TWITTER_MUSICEUMBOT_TOKEN',
    'TWITTER_MUSICEUMBOT_TOKENSECRET',
])->notEmpty();

$guzzleHttpAdapter = new GuzzleHttpAdapter(new Client);
$musicBrainz       = new MusicBrainz($guzzleHttpAdapter);
$releaseFilter = new ReleaseFilter;
$releaseFilter->addReleaseName(new Name('museum'));
$pageFilter = new PageFilter(0, 1);
$releaseList = $musicBrainz->api()->search()->release($releaseFilter, $pageFilter);

die($releaseList);

// Find an recording between 1 and the total of available recordings
$getRecording = $client->request('GET', $mbzApi . '&limit=1&offset=' . $rndId)->getBody();

die($getRecording);

$tweet = $getRecording->title . ' by ' . $getRecording->artistDisplayName . ', but with ...';

die($tweet);

// Twitter API: set up the connection
try
{
    $twitter = new TwitterOAuth(
        $_ENV['TWITTER_MUSICEUMBOT_CONSUMERKEY'],
        $_ENV['TWITTER_MUSICEUMBOT_CONSUMERSECRET'],
        $_ENV['TWITTER_MUSICEUMBOT_TOKEN'],
        $_ENV['TWITTER_MUSICEUMBOT_TOKENSECRET']
    );
}
catch (Exception $e)
{
    die('Twitter: error during the connection.');
}

// Twitter API: post the tweet with the uploaded artwort
try
{
    $twitter->post('statuses/update', [
        'status' => $tweet,
    ]);
}
catch (Exception $e)
{
	die('Twitter: error during tweeting.');
}
unlink($imgPath);
