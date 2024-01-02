<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class AnnouncementElement extends Component
{
    private Announcement $announcement;

    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/custom_loader.css');
        ResourceManager::addStyle('/public/css/announcement/announcement_element.css');
    }

    public function render()
    {
        $id = $this->announcement->getId();
        $name = $this->announcement->getDetails()->getName();
        $kind = $this->announcement->getDetails()->getKind();
        $location = $this->announcement->getDetails()->getLocality();
        $price = $this->announcement->getDetails()->getFormattedPrice();
        $age_type = $this->announcement->getDetails()->getFormattedAge();
        $avatarUrl = $this->announcement->getDetails()->getAvatarUrl();

        echo <<<HTML
        <a class="announcement" href="/announcement/$id">
        <div class="announcement-image" style='background-image: url($avatarUrl);'></div>
        <div class="announcement-data">
            <div class="announcement-detail">
                <div class="flex-center gap-10">
                    <div class="announcement-avatar" style='background-image: url($avatarUrl);'></div>
                    <div class="announcement-name">
                        <div>$name</div>
                        <div>$kind</div>
                    </div>
                </div>
                <div class="flex-center gap-5">
                    <i class="material-icons">location_on</i>
                    <span>$location</span>
                </div>
            </div>
            <hr>
            <div class="announcement-price">
                <div class="flex-center gap-5">
                    <i class="material-icons">date_range</i>
                    <span>$age_type</span>
                </div>
                <div class="flex-center gap-5">
                    <i class="material-icons">account_balance_wallet</i>
                    <span>$price</span>
                </div>
            </div>
        </div>
    </a>
    HTML;
    }
}
