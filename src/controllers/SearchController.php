<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/AnnouncementsRepository.php';
require_once __DIR__ . '/../repository/AnimalTypesRepository.php';
require_once __DIR__ . '/../responses/PostFormResponse.php';
require_once __DIR__ . '/../validation/PostDataValidator.php';

class SearchController extends AppController
{
    private $announcementsRepository;
    private $animalTypesRepository;

    public function __construct()
    {
        parent::__construct();

        $this->announcementsRepository = new AnnouncementsRepository();
        $this->animalTypesRepository = new AnimalTypesRepository();
    }


    public function query_announcements()
    {
        $announcements = $this->filterAnnouncements($this->announcementsRepository->getAnnouncementsToFilter(0), $_GET);
        $this->render('responses/announcements', ['announcements' => $announcements]);
    }

    function filterAnnouncements(array $announcements, $params)
    {
        return array_filter($announcements, function ($announcement) use ($params) {
            /** @var Announcement $announcement */
            $details = $announcement->getDetails();
            $features = AnnouncementDetail::featuresToAssociativeArray($details->getFeatures());

            // FEATURES FILTER
            if (!empty($params['features'])) {
                foreach (explode(';', $params['features']) as $feature) {
                    [$featureId, $value] = explode('-', $feature);
                    if (!isset($features[$featureId]) || $features[$featureId]['value'] != ($value == 2)) {
                        return false;
                    }
                }
            }

            // TYPES FILTER
            if (!empty($params['types']) && !in_array($announcement->getType()->getId(), explode(';', $params['types']))) {
                return false;
            }

            // PRICE & FAVOURITE FILTER
            if (!empty($params['o'])) {
                foreach (explode(';', $params['o']) as $orderOption) {
                    [$orderField, $orderDirection] = explode('-', $orderOption);
                    if ($orderField == 'price') {
                        if ($orderDirection == 1 && $details->getPrice() == null) {
                            return false;
                        } elseif ($orderDirection == 2 && $details->getPrice() != null) {
                            return false;
                        }
                    } else if ($orderField == 'favourite') {
                        if ($orderDirection == 1 && $this->getLoggedUser() && in_array($this->getLoggedUser()->getId(), $details->getLikesIds())) {
                            return false;
                        } elseif ($orderDirection == 2 && $this->getLoggedUser() && !in_array($this->getLoggedUser()->getId(), $details->getLikesIds())) {
                            return false;
                        }
                    }
                }
            }

            // SEARCH FILTER
            if (!empty($params['search'])) {
                $searchTerm = mb_strtolower($params['search']);
                if (
                    str_contains(mb_strtolower($details->getDescription()), $searchTerm) === false &&
                    str_contains(mb_strtolower($details->getName()), $searchTerm) === false &&
                    str_contains(mb_strtolower($details->getLocality()), $searchTerm) === false &&
                    str_contains(mb_strtolower($announcement->getType()->getName()), $searchTerm) === false
                ) {
                    return false;
                }
            }

            return true;
        });
    }

    public function query_animal_types()
    {
        $this->loginRequired();

        $response = new PostFormResponse();

        $validator = new PostDataValidator($this->getPOSTData());
        $validator->addField('search', new SanitizeOnly());
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            $response->setErrorFields($errors);
            $response->send();
        }
        $data = $validator->getSanitizedData();

        $query = $data['search'];

        $animal_types = $this->animalTypesRepository->getAll($query);
        $response->setData($animal_types);
        $response->send();
    }
}
