<?php
namespace Attachments\Controller\Component;

use Attachments\Model\Entity\Attachment;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Attachments component
 */
class AttachmentsComponent extends Component
{
    /**
     * Checks if a downloadAuthorizeCallback was configured and calls it.
     * Will throw an UnauthorizedException if the callback returns false.
     *
     * @param Attachment $attachment Attachment Entity
     * @return void
     * @throws \Cake\Network\Exception\UnauthorizedException if the configured downloadAuthorizeCallback returns false
     */
    public function checkDownloadAuthorization(Attachment $attachment): void
    {
        if ($attachmentsBehavior = $attachment->getRelatedTable()->behaviors()->get('Attachments')) {
            $behaviorConfig = $attachmentsBehavior->config();
            if (is_callable($behaviorConfig['downloadAuthorizeCallback'])) {
                $relatedEntity = $attachment->getRelatedEntity();
                $authorized = $behaviorConfig['downloadAuthorizeCallback']($attachment, $relatedEntity, $this->request);
                if ($authorized !== true) {
                    throw new UnauthorizedException(__d('attachments', 'attachments.unauthorized_for_attachment_download'));
                }
            }
        }
    }
}