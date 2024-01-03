<?php

require_once __DIR__ . '/../repository/UsersRepository.php';
require_once __DIR__ . '/../managers/SessionManager.php';
require_once __DIR__ . '/../utils/utils.php';

class AppController
{
    private $request;

    private ?SessionManager $sessionController;
    private ?User $user;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];

        $this->sessionController = null;
        $this->user = null;
    }

    protected function getLoggedUser(): ?User
    {
        if (!$this->user) {
            $user_id = $this->getSession()->getUserID();
            if (!$user_id) return null;
            $this->user = (new UsersRepository())->getUser(null, $user_id);
        }
        return $this->user;
    }

    protected function getSession(): SessionManager
    {
        if (!$this->sessionController) {
            $this->sessionController = new SessionManager();
        }
        return $this->sessionController;
    }

    protected function loginRequired(): void
    {
        if ($this->getSession()->isLoggedIn()) {
            return;
        }

        $refererPath = $this->isPost() && isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) : '/';
        $redirectUrl = '/login?required&redirect_url=' . urlencode($this->isPost() ? $refererPath : $_SERVER['REQUEST_URI']);

        if ($this->isPost()) {
            $response = new JsonResponse();
            $response->setError('Nie jesteś zalogowany', 401);
            $response->setData(['redirect_url' => $redirectUrl]);
            $response->send();
        } else {
            redirect($redirectUrl);
        }
    }

    protected function adminPrivilegesRequired(): void
    {
        $this->loginRequired();
        $currentUser = $this->getLoggedUser();
        if (!$currentUser->isAdmin()) {
            $response = new JsonResponse();
            $response->setError('Nie masz uprawnień do wykonania tej operacji', 403);
            $response->send();
        }
    }

    protected function getPOSTData()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = new JsonResponse();
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie', 500);
            $response->send();
        }

        return $data;
    }

    protected function isGet(): bool
    {
        return $this->request === 'GET';
    }

    protected function isPost(): bool
    {
        return $this->request === 'POST';
    }

    protected function jsonResponse($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
    }

    protected function render(string $template = null, array $variables = [])
    {
        $templatePath = 'public/views/' . $template . '.php';
        $output = 'File not found';

        if (file_exists($templatePath)) {
            extract($variables);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        print $output;
    }
}
