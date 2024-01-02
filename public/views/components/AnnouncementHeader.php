<?php

class AnnouncementHeader extends Component
{
    private $announcement;
    private $isOwner;
    private $isViewerAdmin;
    private $deleted;
    private $requiresApproval;

    public function __construct($announcement, $isOwner, $isViewerAdmin, $deleted, $requiresApproval)
    {
        $this->announcement = $announcement;
        $this->isOwner = $isOwner;
        $this->isViewerAdmin = $isViewerAdmin;
        $this->deleted = $deleted;
        $this->requiresApproval = $requiresApproval;
    }

    public static function initialize()
    {
    }

    public function render()
    {
        if ($this->deleted) {
            echo $this->renderDeleted();
        } elseif ($this->requiresApproval && !$this->isOwner && !$this->isViewerAdmin) {
            echo $this->renderAwaitingApproval();
        } else {
            echo $this->renderAnnouncement();
        }
    }

    private function renderDeleted()
    {
        $deletedAt = $this->deleted->getDeletedAt()->format('Y-m-d');
        $reason = $this->deleted->getReason() == DeletedAnnouncement::VIOLATION
            ? "ponieważ naruszało nasz regulamin"
            : "Wybrane ogłoszenie zostało zakończone";

        return <<<HTML
        <section class="prompt">
            <span>Wybrane ogłoszenie zostało usunięte przez administratora </span>
            <span class="bold">{$deletedAt}</span>
            <span>{$reason}</span>
        </section>
        HTML;
    }

    private function renderAwaitingApproval()
    {
        return <<<HTML
        <section class="prompt">
            <span>Wybrane ogłoszenie oczekuje na zatwierdzenie, spróbuj ponownie później</span>
        </section>
        HTML;
    }

    private function renderAnnouncement()
    {
        $name = $this->announcement->getDetails()->getName();
        $createdAt = $this->announcement->getCreatedAt()->format('Y-m-d H:i:s');
        $blurClass = $this->requiresApproval && !$this->isViewerAdmin ? 'blur' : '';

        $editButton = $this->isOwner
            ? '<i class="material-icons icon-button action-edit">edit</i>'
            : '';

        $approveButton = $this->requiresApproval && $this->isViewerAdmin
            ? '<i class="material-icons icon-button admin action-approve">assignment_turned_in</i>'
            : '';

        // Przycisk usuwania dla właściciela lub admina
        $deleteButton = ($this->isViewerAdmin || $this->isOwner)
            ? '<i class="material-icons icon-button' . ($this->isViewerAdmin ? ' admin' : '') . ' action-delete">delete_forever</i>'
            : '';

        $reportButton = (!$this->isOwner && !$this->isViewerAdmin) && !$this->announcement->getDetails()->getReportedByCurrentUser()
            ? '<div class="announcement-report action-report">
                            <i class="material-icons">flag</i>
                            <span>Zgłoś ogłoszenie</span>
                           </div>
                           <div class="announcement-report">
                            <i class="material-icons">assignment_turned_in</i>
                            <span>Zgłoszono ogłoszenie</span>
                        </div>'
            : '';

        $reportedStatus = '';
        if ($this->announcement->getDetails()->getReportedByCurrentUser()) {
            $reportedStatus = '<div class="announcement-report">
                                <i class="material-icons">assignment_turned_in</i>
                                <span>Zgłoszono ogłoszenie</span>
                               </div>';
        }

        return <<<HTML
        <section class="announcement-header{$blurClass}">
            <div>
                <span>{$name}</span>
                <span>dodano {$createdAt}</span>
            </div>
            <div>
                {$reportButton}
                {$reportedStatus}
                {$editButton}
                {$approveButton}
                {$deleteButton}
            </div>
        </section>
        HTML;
    }
}
