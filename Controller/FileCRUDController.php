<?php

namespace Awaresoft\FileBundle\Controller;

use Awaresoft\Sonata\AdminBundle\Controller\CRUDController as AwaresoftCRUDController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\PageBlockType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;

/**
 * Class CRUDController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class FileCRUDController extends AwaresoftCRUDController
{
    use ControllerHelperTrait;

    /**
     * @inheritdoc
     */
    public function preDeleteAction($object)
    {
        $message = $this->checkObjectIsBlocked($object, $this->admin);
        $message .= $this->checkObjectHasRelations($object, $this->admin, [
            new PageBlockType($this->container, $object, 'awaresoft.file.block.file', 'fileId'),
        ]);

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function batchActionDeleteIsRelevant(array $idx)
    {
        $message = null;

        foreach ($idx as $id) {
            $object = $this->admin->getObject($id);
            $message = $this->checkObjectIsBlocked($object, $this->admin);
            $message .= $this->checkObjectHasRelations($object, $this->admin, [
                new PageBlockType($this->container, $object, 'awaresoft.file.block.file', 'fileId'),
            ]);
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
