<?php

use LSM\Session;
use LSM\UserException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use LSM\UserMapper;

require __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader('templates');
$view = new Environment($loader);

$config = include 'config/database.php';

$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

$database = new \LSM\Database($dsn, $username, $password);
$userMapper = new UserMapper($database);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$session = new Session();
$sessionMiddleware = function (Request $request, RequestHandlerInterface $handler) use ($session)
{
    $session->start();
    $response = $handler->handle($request);
    $session->save();

    return $response;
};

$app->add($sessionMiddleware);

$app->get('/form', function (Request $request, Response $response, $args) use ($view, $session) {
    if (is_null($session->getData('lang'))) {
        $session->setData('lang', 'ru');
    }
    $body = $view->render('form.twig', [
        'message' => $session->flush('message'),
        'form' => $session->flush('form'),
        'lang' => $session->getData('lang')
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/users', function (Request $request, Response $response, $args) use ($view, $userMapper) {
    $users = $userMapper->getAllUsers();
    if (empty($users)) {
        $body = $view->render('not-found.twig');
    } else {
        $body = $view->render('users.twig', [
            'users' => $users
        ]);
    }
    $response->getBody()->write($body);
    return $response;
});

$app->post('/form', function (Request $request, Response $response, $args) use ($view, $userMapper, $session) {
    $params = (array) $request->getParsedBody();
    try {
        $userMapper->addUser($params);
    } catch (UserException $exception) {
        $session->setData('message', $exception->getMessage());
        $session->setData('form', $params);

        $body = $view->render('form.twig', [
            'message' => $session->flush('message'),
            'form' => $session->flush('form'),
            'lang' => $session->getData('lang')
        ]);

        $response->getBody()->write($body);
        return $response->withStatus(507);
    }

    $body = $view->render('form.twig', [
        'message' => 'Сообщение успешно отправлено',
        'lang' => $session->getData('lang')
    ]);

    $response->getBody()->write($body);
    return $response;
});

$app->get('/lang', function (Request $request, Response $response, $args) use ($view, $session) {
    if ($session->getData('lang') == 'ru') {
        $session->setData('lang', 'en');
    } else {
        $session->setData('lang','ru');
    }
    return $response->withHeader('Location', '/form')->withStatus(302);
});

$app->run();