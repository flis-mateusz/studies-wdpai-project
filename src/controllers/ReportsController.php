<?php

require_once 'AppController.php';
require_once __DIR__ . '/AnnouncementController.php';
require_once __DIR__ . '/../repository/ReportsRepository.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../responses/JsonResponse.php';
require_once __DIR__ . '/../utils/logger.php';


class ReportsController extends AppController
{
    private $reportsRepository;
    private $announcementsRepository;

    public function __construct()
    {
        parent::__construct();

        $this->reportsRepository = new ReportsRepository();
        $this->announcementsRepository = new AnnouncementsRepository();
    }

    public function api_announcement_report()
    {
        $this->loginRequired();
        $response = new JsonResponse();

        $id = AnnouncementController::getPostAnnouncementId($this->getPOSTData());

        $currentUser = $this->getLoggedUser();
        $announcement = $this->announcementsRepository->getAnnouncementWithUserContext($id, $currentUser->getId(), false);

        if (!$announcement || $announcement->isDeleted() || !$announcement->isAccepted()) {
            $response->setError('Ogłoszenie nie istnieje lub zostało usunięte', 400);
            $response->send();
        } else if ($currentUser->getId() == $announcement->getUser()->getId()) {
            $response->setError('Nie możesz zgłosić swojego ogłoszenia', 400);
            $response->send();
        } else if ($announcement->isReporedByUser()) {
            $response->setError('Zgłoszono już to ogłoszenie', 400);
            $response->send();
        }

        try {
            $this->reportsRepository->report($currentUser->getId(), $announcement->getId());
        } catch (Exception $e) {
            $response->setError('Wystąpił wewnętrzny błąd, spróbuj ponownie później', 500);
            $response->send();
        }
        $response->setData('reported');
        $response->send();
    }

    public function api_announcement_reject_reports()
    {
        $this->adminPrivilegesRequired();
        $response = new JsonResponse();
        $id = AnnouncementController::getPostAnnouncementId($this->getPOSTData());

        $this->reportsRepository->rejectReports($id);
        $response->setData('reports-rejected');
        $response->send();
    }
}
