<?php
function get_header()
{
    include_once('./views/header.php');
}

function footer()
{
    include_once('./views/footer.php');
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
    include_once('./views/' . ROUTE_LOGIN . '.php');
    footer();
}

function userLogin()
{
    if (is_logged_in()) {
        header('Location: ' . SUB_ROOT . ROUTE_ADMIN);
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
            header('Location: ' . SUB_ROOT . ROUTE_ADMIN);
        }
    } else {
        unset($_SESSION['error']);
        unset($_SESSION['errorMsg']);
    }

    loginView();
}

function userHome()
{
    get_header();
    include_once('./views/' . ROUTE_HOME . '.php');
    footer();
}

function logout()
{
    unset($_SESSION['userId']);
    header('Location: ' . SUB_ROOT . ROUTE_HOME);
}

function getStatement()
{
    if (isset($_SESSION['user'])) {
        // go to capitals page if same user comes again
        header("Location: " . ROUTE_RANDOM);
    }

    // see if a chain exists that is idle and incomplete
    $chain = getChain(0, 0);

    if (!$chain) {
        return createNewFlow();
    } else {
        $chainId = $chain['chainId'];
        $place = getUsersCountByChain($chainId);
        $place++;

        if ($place > 2) {
            // create new chain.
            return createNewFlow();
        }

        // get last users text 
        $userId = $chain['user'];
        $user = getUserById($userId);

        // create user
        $userId = createUser($chainId, $place);
        // set chain status as in-process
        updateChainStatus($chainId, 1);

        setSession($userId, $chainId, $user['answer']);
        // $msg = fetchMessageByChain($chainId);
        return $user['answer'];
    }
}

function createNewFlow()
{
    $msg = fetchRandomMessage();
    // create new chain
    $chainId = createChain($msg['id']);
    $place = getUsersCountByChain($chainId);
    $place++;
    // create user
    $userId = createUser($chainId, $place);
    setSession($userId, $chainId, $msg['msg']);
    return $msg['msg'];
}

function setSession($userId, $chainId, $msg)
{
    $_SESSION['user'] = $userId;
    $_SESSION['chainId'] = $chainId;
    $_SESSION['message'] = $msg;
}

function unsetSession()
{
    unset($_SESSION['user']);
    unset($_SESSION['chainId']);
    unset($_SESSION['message']);
}

function saveReminder()
{
    $answer = $_POST['reminder'] ?? '';
    $time = $_POST['time'] ?? '';
    if (empty($answer) || empty($time)) {
        echo false;
        return false;
    }

    $userId = $_SESSION['user'];
    $chainId = $_SESSION['chainId'];
    // feed answer of this user
    saveUserAnswer($userId, $answer, $time);
    // mark this user as last one in chain

    $place = getUsersCountByChain($chainId);

    $completed = $place == PARTICIPANT_LIMIT ? 1 : 0;
    $status = 0; // idle

    updateChain($chainId, $userId, $status, $completed);
    unsetSession();

    $redirectURL = str_replace("{userId}", $userId, REDIRECT_URL);
    echo $redirectURL;
}

function array_to_csv_download($array, $filename = "export.csv", $delimiter = ",")
{
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    // open raw memory as file so no temp files needed, you might run out of memory though
    $f = fopen('php://output', 'w');
    // loop over the input array
    foreach ($array as $line) {
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter);
    }
    // make php send the generated csv lines to the browser
    fpassthru($f);
}

function downloadCSV(){
    $data = getUsersCSV();
    array_to_csv_download($data);
}