<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require __DIR__.'/../vendor/autoload.php';

use Intervention\Image\ImageManager as Image;
use Goutte\Client;
use Abraham\TwitterOAuth\TwitterOAuth;

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$dotenv->required([
    'TWITTER_LOUVROBOT_CONSUMERKEY',
    'TWITTER_LOUVROBOT_CONSUMERSECRET',
    'TWITTER_LOUVROBOT_TOKEN',
    'TWITTER_LOUVROBOT_TOKENSECRET',
])->notEmpty();

$louvreApi  = 'http://cartelfr.louvre.fr/cartelfr/visite?srv=car_not&idNotice=';
$louvreUrl  = 'http://cartelfr.louvre.fr/cartelfr/visite?srv=car_not_frame&idNotice=';
$clsArtist  = 'font.nom_cartel';
$clsTitle   = 'font.titre_cartel';
$clsDate    = 'font.dates_cartel';
$clsImage   = 'td.legende a img';
$clsCartel  = 'font.text_cartel';
$clsDepartement = 'td.departement_cartel[valign="top"]';
$clsInventaire = 'div.departement_cartel[align="right"]';

// Musee du Louvre noticeId 602 / 25608
// Pick a random noticeId
$rndMin = 602;
$rndMax = 25608;
$rndId = mt_rand($rndMin, $rndMax);

$client = new Client();
$crawler = $client->request('GET', $louvreApi . $rndId);

// WebCrawl for the artwork's informations
$twtArtist = ($crawler->filter($clsArtist)->count() > 0) ? $crawler->filter($clsArtist)->text() : 'Anonyme';
$twtTitle = ($crawler->filter($clsTitle)->count() > 0) ? $crawler->filter($clsTitle)->html() : 'sans titre';
$twtDate = ($crawler->filter($clsDate)->count() > 0) ? $crawler->filter($clsDate)->text() : 'sans date';
$twtImage = ($crawler->filter($clsImage)->count() > 0) ? 'http://cartelfr.louvre.fr' . $crawler->filter($clsImage)->attr('src') : '';
$twtCartel = ($crawler->filter($clsImage)->count() > 0) ? $crawler->filter($clsImage)->attr('src') : '';
$dbDepartement = ($crawler->filter($clsDepartement)->count() > 0) ? $crawler->filter($clsDepartement)->text() : 'nodepart';
$dbInventaire = ($crawler->filter($clsInventaire)->count() > 0) ? $crawler->filter($clsInventaire)->text() : 'noinv';

// Variables cleaning
// Cleaning the datation
$findMe = [
    '/Vers/',
    '/Milieu/',
    '/Début/',
    '/Fin/',
    '/Oeuvre/',
    '/Epoque/',
    '/Premier/',
    '/1re/',
    '/Second/',
    '/Deuxi/',
    '/2e/',
];
$changeMe = [
    'vers',
    'milieu',
    'début',
    'fin',
    'oeuvre',
    'époque',
    'premier',
    'première',
    'second',
    'deuxième',
    'seconde',
];
$twtTitle = str_replace('<br>', ' ', $twtTitle);
$twtDate = str_replace('<br>', ' ', $twtDate);
$twtDate = preg_replace($findMe, $changeMe, $twtDate);
$twtImage = str_replace('x200_', '', $twtImage);

// Cleaning the department's name
$findDep = ['/département de /', '/département des /'];
$changeDep = ['', ''];
$dbDepartement = html_entity_decode(preg_replace($findDep, $changeDep, strtolower($dbDepartement)));

// Image content
$imgFile = substr($twtImage, strrpos($twtImage, '/') + 1);
$imgData = file_get_contents($twtImage);
$imgPath = __DIR__.'/../storage/louvrobot-img/' . $imgFile;
try {
    file_put_contents($imgPath, $imgData);
}
catch (Exception $e) {
    die('Image: no data for the artwork');
}

// Generate the tweet
$tweet = $twtArtist . ', ' . strip_tags($twtTitle) . ', ' . $twtDate . '. ' . $louvreUrl . $rndId;

// Twitter API: set up the connection
try {
    $twitter = new TwitterOAuth(
        $_ENV['TWITTER_LOUVROBOT_CONSUMERKEY'],
        $_ENV['TWITTER_LOUVROBOT_CONSUMERSECRET'],
        $_ENV['TWITTER_LOUVROBOT_TOKEN'],
        $_ENV['TWITTER_LOUVROBOT_TOKENSECRET']
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
