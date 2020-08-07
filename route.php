<?php
class Routes
{
    public function configureRoutes()
    {
        $action = $_SERVER['REQUEST_URI'];
        $action = trim($action, '/');
        $params = explode('/', $action);
        $index = 0;
        if ($params[0] === SUB_ROOT && SUB_ROOT !== '') {
            $index++;
        }
        $route = isset($params[$index]) ? $params[$index] : ROUTE_HOME;

        self::normalRoutes($route);
    }

    public function normalRoutes($route)
    {
        switch ($route) {
            case ROUTE_LOGIN:
                userLogin();
                break;
            case ROUTE_LOGOUT:
                logout();
                break;
            case ROUTE_HOME:
                userHome();
                break;
            case ROUTE_STATEMENT:
                get_header();
                include_once('./views/' . ROUTE_HOME . '.php');
                include_once('./views/' . ROUTE_STATEMENT . '.php');
                footer();
                break;
            case ROUTE_RANDOM:
                get_header();
                include_once('./views/' . ROUTE_RANDOM . '.php');
                footer();
                break;
            case ROUTE_REMINDER:
                get_header();
                include_once('./views/' . ROUTE_REMINDER . '.php');
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
            case ROUTE_SAVE_REMINDER:
                saveReminder();
                break;
            case ROUTE_EXPORT:
                downloadCSV();
                break;
            default:
                userHome();
        }
    }
}
