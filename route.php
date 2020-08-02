<?php
class Routes
{
    public function configureRoutes()
    {
        $action = $_SERVER['REQUEST_URI'];
        $action = trim($action, '/');
        $params = explode('/', $action);
        $index = 0;
        $route = $params[$index] ? $params[$index] : ROUTE_HOME;
        $id = isset($params[$index + 1]) ? $params[$index + 1] : '';

        if ($id) {
            self::idRoutes($route, $id);
        } else {
            self::normalRoutes($route);
        }
    }

    public function idRoutes($route, $id)
    {
        // $id is used in the view
        /* if (explode(':', $id)[0] === 'new') {
            if (!is_logged_in()) {
                header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
            }

            $id = explode(':', $id)[1];
            $id = explode('?', $id)[0];
            $removeImageIds = explode(',', $_GET['removed']);

            if ($route === ROUTE_GUNBROKER) {
                $item = getItemDetail($id);
            } else if ($route === ROUTE_ARMSLIST) {
                $item = getArmslistItemDetail($id);
            }

            $recordedImages = $_SESSION['images'];
            $images = array();

            foreach ($recordedImages as $key => $image) {
                if (array_search($key, $removeImageIds) === false) {
                    $images[] = $image;
                }
            }

            if (count($images)) {
                $_SESSION['images'] = $images;
            }

            include_once("./ragic/create-record.php");

            require_once('./models/enteries-model.php');
            $entriesObj = new EnteriesModel();
            $entriesObj->itemId = $id;
            $entriesObj->type = 1;
            $entriesObj->addEntry($id);
            return;
        } */

        /* switch ($route) {
            case ROUTE_GUNBROKER:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }
                $item = getItemDetail($id);
                include_once('./views/item-detail.php');
                break;
            case ROUTE_ARMSLIST:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }
                $item = getArmslistItemDetail($id);
                include_once('./views/item-detail.php');
                break;
            case ROUTE_ALTER_ITEM:
                require_once('./models/enteries-model.php');
                $entriesObj = new EnteriesModel();
                $ids = explode('?', $id);
                $id = $ids[0];
                $entriesObj->itemId = $id;
                $entriesObj->type = $_GET['type'];
                echo $entriesObj->addEntry($id);
                break;
            case ROUTE_DELETE_USER:
                require_once('./models/user-detail-model.php');
                $userObj = new UserDetailModel();
                $userObj->userId = $id;
                echo $userObj->removeUser($id);
                break;
            case ROUTE_DELETE_KEYWORD:
                require_once('./models/keyword-model.php');
                $userObj = new KeywordModel();
                $userObj->id = $id;
                echo $userObj->removeKeyword($id);
                break;
            case ROUTE_DELETE_INSTAGRAM:
                require_once('./models/instagram-model.php');
                $userObj = new InstagramModel();
                $userObj->id = $id;
                echo $userObj->removeIgUser();
                break;
            default:
                userHome();
        } */
    }

    public function normalRoutes($route)
    {
        switch ($route) {
            case ROUTE_HOME:
                get_header();
                include_once('./views/' . ROUTE_HOME . '.php');
                footer();
                break;
            case ROUTE_STATEMENT:
                get_header();
                include_once('./views/' . ROUTE_HOME . '.php');
                include_once('./views/' . ROUTE_STATEMENT . '.php');
                footer();
                break;
            case ROUTE_LOGOUT:
                logout();
                break;
            case ROUTE_LOGIN:
                get_header();
                include_once('./views/' . ROUTE_LOGIN . '.php');
                footer();
                break;
            case ROUTE_ADMIN:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }

                get_header();
                include_once('./views/' . ROUTE_ADMIN . '.php');
                footer();
                break;
            case ROUTE_GUNBROKER:

                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }
                include_once('./views/' . ROUTE_GUNBROKER . '/items-listing.php');
                break;
            case ROUTE_ARMSLIST:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }
                include_once('./views/' . ROUTE_ARMSLIST . '/items-listing.php');
                break;
            case ROUTE_ALL:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }

                $showAll = true;

                include_once('./views/' . ROUTE_GUNBROKER . '/items-listing.php');
                include_once('./views/' . ROUTE_ARMSLIST . '/items-listing.php');
                break;
            case ROUTE_SYNC_RAGIC:
                if (!is_logged_in()) {
                    header('Location: ' . SUB_ROOT . ROUTE_LOGIN);
                }
                include_once("./ragic/sync.php");
                break;
            case ROUTE_USERS:
                get_header();
                get_users();
                footer();
                break;
            case ROUTE_ADD_USER:
                addUser();
                break;
            case ROUTE_KEYWORDS:
                get_header();
                get_keywords();
                footer();
                break;
            case ROUTE_ADD_KEYWORD:
                addKeyword();
                break;
            case ROUTE_INSTAGRAM:
                get_header();
                get_ig_users();
                footer();
                break;
            case ROUTE_ADD_INSTAGRAM:
                addIGUser();
                break;
            case ROUTE_PROCESS_INSTAGRAM:
                processIg();
                break;
            default:
                userHome();
        }
    }
}
