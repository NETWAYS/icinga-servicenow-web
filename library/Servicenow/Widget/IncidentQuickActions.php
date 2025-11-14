<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Incident;

use Icinga\Web\Session;
use Icinga\Web\Notification;
use ipl\Html\Form;
use ipl\Web\Common\CsrfCounterMeasure;

class IncidentQuickActions extends Form
{
    use Database;
    use CsrfCounterMeasure;

    protected $defaultAttributes = [
        'class' => ['inline', 'quick-actions'],
        'name' => 'incident-quick-actions'
    ];

    /** @var Incident */
    protected $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function hasBeenSubmitted(): bool
    {
        return $this->hasBeenSent() && $this->getPressedSubmitElement();
    }

    // TODO: Not yet implemented
    // protected function assembleResolveButton(): void
    // {
    //     $this->addElement(
    //         'submitButton',
    //         'manage',
    //         [
    //             'class' => ['control-button', 'spinner'],
    //             'label' => [t('Resolve')],
    //             'title' => t('Resolve this incident in ServiceNow')
    //         ]
    //     );
    // }

    protected function assembleDeleteButton(): void
    {
        $this->addElement(
            'submitButton',
            'delete',
            [
                'class' => ['control-button', 'confirm-button', 'spinner'],
                'label' => [t('Delete')],
                'data-confirmation' => t('Confirm'),
                'title' => t('Delete this incident from the database. This will not affect the incident in ServiceNow')
            ]
        );
    }

    protected function assemble()
    {
        $this->addElement($this->createCsrfCounterMeasure(Session::getSession()->getId()));
        $this->assembleDeleteButton();
        // $this->assembleResolveButton();
    }

    protected function onSuccess()
    {
        $pressedButton = $this->getPressedSubmitElement()->getName();

        switch ($pressedButton) {
            case 'delete':
                $this->deleteIncident();
                break;
        }
    }

    protected function deleteIncident(): void
    {
        $db = $this->getDb();

        $db->beginTransaction();

        try {
            $db->delete('incident', [
                'id = ?' => $this->incident->id,
            ]);
        } catch (Exception $e) {
            $db->rollBackTransaction();
            Notification::error(t('Failed to delete incident'));

            return;
        }

        $db->commitTransaction();
        Notification::success(t('Deleted incident'));
    }
}
