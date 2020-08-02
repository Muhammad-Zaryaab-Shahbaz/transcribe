<?php
function get_header()
{
    include_once('./views/header.php');
}

function footer()
{
    include_once('./views/footer.php');
}

function get_users($errors = false, $errorMsg = "")
{
    $addUser = true;
    $showUsers = true;
    require_once('./models/user-detail-model.php');
    $userObj = new UserDetailModel();
    $users = $userObj->users();
    include_once('./views/users.php');
}

function get_keywords($errors = false, $errorMsg = "")
{
    $addUser = true;
    $showUsers = true;
    require_once('./models/keyword-model.php');
    $keywordObj = new KeywordModel();
    $keywords = $keywordObj->keywords();
    include_once('./views/keywords.php');
}

function get_ig_users($errors = false, $errorMsg = "")
{
    $addUser = true;
    $showUsers = true;
    require_once('./models/instagram-model.php');
    $keywordObj = new InstagramModel();
    $users = $keywordObj->igUsers();
    include_once('./views/instagram.php');
}
///////////////////////////////////////////////////////////
///////////// Check Login /////////////
///////////////////////////////////////////////////////////
function is_logged_in()
{
    return isset($_SESSION['userId']) && !empty($_SESSION['userId']);
}

function loginView()
{
    get_header();
    include_once('./views/login.php');
    footer();
}

function userLogin()
{
    if (is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_HOME);
    }

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'] ? $_POST['username'] : null;
        $password = $_POST['password'] ? $_POST['password'] : null;

        if (!isset($username) || !isset($password)) {
            $_SESSION['error'] = 'error';
            $_SESSION['errorMsg'] = 'Invalid login details.';
            loginView();
            return;
        }

        if (login($username, hash('sha256', $password))) {
            header('Location: ' . SUB_ROOT . ROUTE_HOME);
        }
    } else {
        unset($_SESSION['error']);
        unset($_SESSION['errorMsg']);
    }

    loginView();
}

function userHome()
{
    if (!is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
    }
    get_header();
    include_once('./views/home.php');
    footer();
}

function logout()
{
    unset($_SESSION['userId']);
    header('Location: ' . SUB_ROOT . ROUTE_HOME);
}

function addUser()
{
    $addUser = true;
    if (!is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
        return;
    }

    $email = $_POST['username'] ?? '';
    $pwd = $_POST['password'] ?? '';

    if (empty($email) || empty($pwd)) {
        header('Location: ' . SUB_ROOT . ROUTE_USERS);
        return;
    }

    $userExists = fetchUserByUsername($email);

    if ($userExists) {
        get_header();
        get_users(true, "(User already exists)");
        footer();
        return;
    }

    require_once('./models/user-detail-model.php');
    $userObj = new UserDetailModel();
    $userObj->username = $email;
    $userObj->pwd = hash('sha256', $pwd);

    if ($userObj->addUser()) {
        header('Location: ' . SUB_ROOT . ROUTE_USERS);
    } else {
        get_header();
        get_users(true, "(Unexpected Error)");
        footer();
    }
}

function addKeyword()
{
    $addUser = true;
    if (!is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
        return;
    }

    $keyword = $_POST['keyword'] ?? '';

    if (empty($keyword)) {
        header('Location: ' . SUB_ROOT . ROUTE_KEYWORDS);
        return;
    }

    $keywordExists = fetchKeyword($keyword);

    if ($keywordExists) {
        get_header();
        get_keywords(true, "(Keyword already exists)");
        footer();
        return;
    }

    require_once('./models/keyword-model.php');
    $userObj = new KeywordModel();
    $userObj->keyword = $keyword;

    if ($userObj->addKeyword()) {
        header('Location: ' . SUB_ROOT . ROUTE_KEYWORDS);
    } else {
        get_header();
        get_keywords(true, "(Unexpected Error)");
        footer();
    }
}

function addIgUser()
{
    $addUser = true;
    if (!is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
        return;
    }

    $keyword = $_POST['instagram'] ?? '';

    if (empty($keyword)) {
        header('Location: ' . SUB_ROOT . ROUTE_INSTAGRAM);
        return;
    }

    $keywordExists = fetchIgProfile($keyword);

    if ($keywordExists) {
        get_header();
        get_ig_users(true, "(Profile already exists)");
        footer();
        return;
    }

    require_once('./models/instagram-model.php');
    $userObj = new InstagramModel();
    $userObj->name = $keyword;

    if ($userObj->addIgUser()) {
        header('Location: ' . SUB_ROOT . ROUTE_INSTAGRAM);
    } else {
        get_header();
        get_ig_users(true, "(Unexpected Error)");
        footer();
    }
}

function processIg()
{

    global $PDO;

    require_once('./models/instagram-model.php');
    $obj = new InstagramModel();
    echo "Processing... <br/>";

    echo "Fetching records from database... <br/>";
    $all_users = $obj->igUsers();
    $chunkSize = 100; // changed to 100
    $chunkCnt = 1;
    $totalFeeds = 0;
    $feedsInserted = 0;

    while (true) {

        $user_chunk = array_slice($all_users, $chunkSize * ($chunkCnt - 1), $chunkSize);

        if ($user_chunk == false) break 1;

        foreach ($user_chunk as $key => $user) {

            echo ($key + 1) . ". Fetching instagram detail of each record... <br/>";

            $person = new InstagramModel();
            // 1. Fetch user info
            $person->getInstagramNoApi($user['name']);

            // 4. For every photo in database
            $query = "SELECT * FROM feed WHERE userId = " . $user['id'];
            $stmnt = $PDO->prepare($query);
            if (!$stmnt->execute()) {
                throw new Exception('QUERY FAILED: ' . serialize($stmnt->errorInfo()) . '...' . $query);
                return false;
            }

            // Increment Total Feeds
            if ($person->feeds && count($person->feeds)) {
                $totalFeeds += count($person->feeds);
            }

            $existingFeeds = array();
            while ($row = $stmnt->fetch(PDO::FETCH_ASSOC)) {
                $existingFeeds[] = $row['imageId'];
            }

            $insertFeedData = array();
            echo "Comparing feed with database... <br/>";
            if ($person->feeds && count($person->feeds)) {
                foreach ($person->feeds as $feed) {
                    if (is_numeric(array_search($feed->node->id, $existingFeeds))) {
                        // feed already exists in database
                    } else {
                        $feedsInserted++;
                        // new feed spotted, insert in feed
                        $insertFeedData[] = $feed;
                    }
                }
            }

            // If new feeds have been fetched
            if (count($insertFeedData)) {
                $FeedQMarks = implode(' , ', array_fill(0, count($insertFeedData), '( ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NOW())'));
                $FeedQuery = "INSERT INTO `feed` (userId, imageId, imgUrl, caption, uploadedOn, updatedOn, createdOn) VALUES {$FeedQMarks} ";
                $FeedStmnt = $PDO->prepare($FeedQuery);

                $Count = 0;
                $num = 5;

                foreach ($insertFeedData as $feed) {

                    $text = isset($feed->node) && isset($feed->node->edge_media_to_caption)
                        && isset($feed->node->edge_media_to_caption->edges) && isset($feed->node->edge_media_to_caption->edges[0])
                        && isset($feed->node->edge_media_to_caption->edges[0]->node)
                        && isset($feed->node->edge_media_to_caption->edges[0]->node->text) ?
                        $feed->node->edge_media_to_caption->edges[0]->node->text : "";

                    $FeedStmnt->bindValue($Count * $num + 1, $user['id']);
                    $FeedStmnt->bindValue($Count * $num + 2, $feed->node->id);
                    $FeedStmnt->bindValue($Count * $num + 3, $feed->node->thumbnail_src);
                    $FeedStmnt->bindValue($Count * $num + 4, $text);
                    $FeedStmnt->bindValue($Count * $num + 5, $feed->node->taken_at_timestamp);

                    $Count++;
                }

                echo "Inserting new feed in database... <br/>";
                if (!$FeedStmnt->execute()) {
                    throw new Exception('QUERY FAILED: ' . serialize($FeedStmnt->errorInfo()) . '...' . $FeedQuery);
                    return false;
                }


                require_once('./ragic/create-notes-record.php');

                foreach ($insertFeedData as $feed) {
                    echo "Inserting record in ragic... <br/>";

                    $_SESSION['note'] = $text;
                    $_SESSION['uploadedOn'] = $feed->node->taken_at_timestamp;
                    $_SESSION['image'] = $feed->node->thumbnail_src;

                    if (!isset($_SESSION['note']) || !isset($_SESSION['uploadedOn'])) {
                        return false;
                    }

                    $ckfile = tempnam("/tmp", "CURLCOOKIE");  //create cookie file

                    //Authentication
                    $account = RAGIC_ACCOUNT;  //fill your account info
                    $password = RAGIC_PASS; //fill your password info

                    $PostData = "api&v=3&u=" . $account . "&p=" . $password . "&login_type=sessionId";
                    $_SESSION["SessionId"] = Curl(RAGIC_AUTH_URL, $ckfile, $PostData);
                    
                    $Url = RAGIC_NOTES_URL . RAGIC_API;

                    $PostData = [
                        $source => "Instagram",
                        $note => $_SESSION['note'],
                        $uploadedOn => $_SESSION['uploadedOn']
                    ];

                    $json = Curl($Url, $ckfile, $PostData);
                    $result = json_decode($json, true);
                    if ($result["status"] != "ERROR") {
                        uploadImages($ckfile, $result["ragicId"], array($_SESSION['image']));
                    } else {
                        echo json_encode(array("success" => false, "msg" => $result["msg"]));
                    }


                    echo "Inserted record in ragic... <br/>";
                }
            }
        }

        $chunkCnt++;
    }

    echo $feedsInserted . " feeds added. <br/>";

    echo "Done";
}
