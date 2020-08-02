<?php

///////////////////////////////////////////////////////////
///////////// Set session or return false /////////////
///////////////////////////////////////////////////////////
function login($email, $pwd)
{
    $user = fetchUser($email, $pwd);
    if (!$user) {
        $_SESSION['error'] = 'error';
        $_SESSION['errorMsg'] = 'Invalid login details.';
        return false;
    }

    $_SESSION['errorMsg'] = '';
    $_SESSION['userId'] = $user['userId'];
    return $user;
}

function fetchUserDetail($userId)
{
    global $PDO;
    $query = "SELECT * FROM user_detail WHERE id = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $userId);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row ? $row : false;
}

function fetchUser($username, $pwd)
{
    global $PDO;
    $query = "SELECT * FROM users WHERE username = ? AND pwd = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $username);
    $stmnt->bindValue(2, $pwd);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    $row = fetchUserDetail($row['userId']);
    return $row ? $row : false;
}

function fetchUserByUsername($username)
{
    global $PDO;
    $query = "SELECT * FROM users WHERE username LIKE ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $username);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row;
}

function fetchKeyword($keyword)
{
    global $PDO;
    $query = "SELECT * FROM keywords WHERE keyword LIKE ? AND userId = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $keyword);
    $stmnt->bindValue(2, $_SESSION['userId']);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row;
}

function fetchIgProfile($keyword)
{
    global $PDO;
    $query = "SELECT * FROM instagram WHERE name LIKE ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $keyword);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row;
}

///////////////////////////////////////////////////////////
///////////// Get Items Listing /////////////
///////////////////////////////////////////////////////////
function getItems($word, $site = GUN_BROKER)
{
    $name = 'LIST_ENDPOINT_' . $site;
    $url = str_replace('{word}', $word, constant($name));
    $allOrigins = "https://api.allorigins.win/get?url=" . urlencode($url);
    $curl = curl_init($allOrigins);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT, 60
    ]);
    $result = curl_exec($curl);
    if (!$result) {
        return json_encode(array());
    } else {
        return json_decode($result)->contents;
    }
}

///////////////////////////////////////////////////////////
///////////// Parse Armslist one record //////////////////
///////////////////////////////////////////////////////////
function parseArmslistRecords($data)
{
    $re = '/\/posts\/(\d+)\//';
    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($data);
    libxml_use_internal_errors(false);

    $xpath = new \DOMXpath($doc);
    $items = array();

    $listContainer = $doc->getElementById("bootstrap-overrides");
    $records = $xpath->query('//div/div[@class="container-fluid"]', $listContainer);

    foreach ($records as $record) {
        $itemDetail = new ItemDetailModel();
        $image = $xpath->query('.//img[@class="img-responsive"]', $record);

        if (!isset($image[0])) {
            continue;
        }

        $itemDetail->images = $image[0]->getAttribute("src");

        $rightBox = $xpath->query('.//div[@class="col-md-7"]', $record);
        $rightBox = $rightBox[0];

        $title = $rightBox->getElementsByTagName('a');
        $itemDetail->title = $title[0]->nodeValue;

        $id = $title[0]->getAttribute("href");
        preg_match_all($re, $id, $matches, PREG_SET_ORDER, 0);
        $matches =  $matches[0];
        $itemDetail->id = $matches[1];

        $price = $rightBox->getElementsByTagName('h4');
        $itemDetail->price = isset($price[1]) ? $price[1]->nodeValue : '';

        $purpose = $rightBox->getElementsByTagName('small');
        $itemDetail->purpose = isset($purpose[0]) ? $purpose[0]->nodeValue : '';
        $itemDetail->location = isset($purpose[1]) ? $purpose[1]->nodeValue : '';
        $itemDetail->endingDate = isset($purpose[2]) ? $purpose[2]->nodeValue : '';

        $items[] = $itemDetail;
    }

    return $items;
}

///////////////////////////////////////////////////////////
///////////// Get Items Listing /////////////
///////////////////////////////////////////////////////////
function getArmslistItemDetail($id)
{
    $url = str_replace('{id}', $id, constant('ITEM_DETAIL_' . ARMS_LIST));
    $allOrigins = "https://api.allorigins.win/get?url=" . urlencode($url);
    $curl = curl_init($allOrigins);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT, 60
    ]);
    $result = curl_exec($curl);
    if (!$result) {
        return false;
    } else {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(json_decode($result)->contents);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXpath($doc);
        $itemDetail = new ItemDetailModel();

        $titlePath = $doc->getElementsByTagName('h1');
        $title = $titlePath[0]->nodeValue;
        $titleArr = explode(':', $title);
        $itemDetail->title = isset($titleArr[1]) ? $titleArr[1] : $title;

        $descriptionPath = $xpath->query('//div[@class="postContent"]');
        $itemDetail->description = trim(preg_replace('/\s\s+/', ' ', $descriptionPath[0]->nodeValue));

        $itemDetail->id = $id;

        /* $endingDatePath = $doc->getElementById("EndingDate");
        if (isset($endingDatePath))
            $itemDetail->endingDate = trim(preg_replace('/\s\s+/', ' ', $endingDatePath->nodeValue)); */
        $itemDetail->endingDate = '';

        $location = $xpath->query('//ul[@class="location"]');
        $locationPath = $xpath->query(".//div[2]", $location[0]);
        $itemDetail->location = str_replace("\r\n", "", $locationPath[0]->nodeValue);

        $pricePath = $xpath->query('//span[@class="price"]');
        $itemDetail->price = trim(preg_replace('/\s\s+/', ' ', $pricePath[0]->nodeValue));

        $itemDetail->url = $url;

        /* $ownerPath = $xpath->query('//span[@class="user-name"]/text()');
        $itemDetail->owner = trim(preg_replace('/\s\s+/', ' ', $ownerPath[0]->nodeValue)); */
        $itemDetail->owner = '-';

        $imageContainer = $xpath->query('//div[@class="pagination-slideset"]');
        $imageBoxes = $xpath->query('.//div[@class="item"]', $imageContainer[0]);

        $links = [];
        foreach ($imageBoxes as $container) {
            $image = $container->getElementsByTagName("img");
            // if (count($links) < 18) {
            $links[] = $image[0]->getAttribute("src");
            // }
        }
        $itemDetail->images = $links;

        $object = json_decode(json_encode($itemDetail));
        foreach ($object as $key => $val) {
            $_SESSION[$key] = $val;
        }
        $_SESSION['social'] = SOCIAL_ARMS_LIST;
        return $object;
    }
}

///////////////////////////////////////////////////////////
/////////////////// Get Items Listing /////////////////////
///////////////////////////////////////////////////////////
function getItemDetail($id)
{
    $url = str_replace('{id}', $id, constant('ITEM_DETAIL_' . GUN_BROKER));
    $allOrigins = "https://api.allorigins.win/get?url=" . urlencode($url);
    $curl = curl_init($allOrigins);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT, 60
    ]);
    $result = curl_exec($curl);
    if (!$result) {
        return false;
    } else {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(json_decode($result)->contents);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXpath($doc);
        $itemDetail = new ItemDetailModel();

        $titlePath = $xpath->query('//h1[@class="item-title"]');
        $itemDetail->title = $titlePath[0]->nodeValue;

        $descriptionPath = $xpath->query('//div[@class="item-description-container"]');
        $itemDetail->description = trim(preg_replace('/\s\s+/', ' ', $descriptionPath[0]->nodeValue));

        $idPath = $doc->getElementById("tdItemID");
        $itemDetail->id = trim(preg_replace('/\s\s+/', ' ', $idPath->nodeValue));

        $endingDatePath = $doc->getElementById("EndingDate");
        if (isset($endingDatePath))
            $itemDetail->endingDate = trim(preg_replace('/\s\s+/', ' ', $endingDatePath->nodeValue));

        $locationPath = $doc->getElementById("tdLocation");
        $itemDetail->location = str_replace("\r\n", "", $locationPath->nodeValue);

        $pricePath = $doc->getElementById("fixed-price");
        if (!$pricePath) {
            $pricePath = $doc->getElementById("CurrentBid");
        }

        $itemDetail->price = trim(preg_replace('/\s\s+/', ' ', $pricePath->nodeValue));

        $itemDetail->url = urldecode(explode('url=', $allOrigins)[1]);

        $ownerPath = $xpath->query('//span[@class="user-name"]/text()');
        $itemDetail->owner = trim(preg_replace('/\s\s+/', ' ', $ownerPath[0]->nodeValue));

        $imageBoxes = $xpath->query('//*[@id="carousel-view-item"]/div[1]/div[1]/div');

        $links = [];
        foreach ($imageBoxes as $container) {
            $arr = $container->getElementsByTagName("a");
            $image = $arr[0]->getElementsByTagName('img');
            // if (count($links) < 18) {
            $links[] = $image[0]->getAttribute("src");
            // }
        }
        $itemDetail->images = $links;

        $object = json_decode(json_encode($itemDetail));
        foreach ($object as $key => $val) {
            $_SESSION[$key] = $val;
        }
        $_SESSION['social'] = SOCIAL_GUN_BROKER;
        return $object;
    }
}
