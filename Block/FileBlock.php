<?php

namespace Awaresoft\FileBundle\Block;

use Awaresoft\Sonata\BlockBundle\Block\BaseBlockService;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Awaresoft\FileBundle\Admin\FileAdmin;
use Awaresoft\FileBundle\Entity\File;
use Awaresoft\FileBundle\Entity\FileRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FileBlock
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class FileBlock extends BaseBlockService
{
    /**
     * @var FileAdmin
     */
    protected $fileAdmin;

    /**
     * Set default settings
     *
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'AwaresoftFileBundle:Block:block_file.html.twig',
            'fileId' => null,
            'header' => null
        ));
    }

    /**
     * @param FormMapper $formMapper
     * @param BlockInterface $block
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if (!$block->getSetting('fileId') instanceof File) {
            $this->load($block);
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('header', 'text', array(
                    'required' => false,
                    'sonata_help' => $this->getFileAdmin()->trans('file.block.help.file_header_block'),
                )),
                array($this->getFileBuilder($formMapper), null, array())
            ),
        ));
    }

    /**
     * Execute block
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), array(
            'file' => $this->findById($blockContext->getBlock()->getSetting('fileId')),
            'header' => $blockContext->getBlock()->getSetting('header'),
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $block->setSetting('fileId', is_object($block->getSetting('fileId')) ? $block->getSetting('fileId')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $block->setSetting('fileId', is_object($block->getSetting('fileId')) ? $block->getSetting('fileId')->getId() : null);
    }

    /**
     * @param FormMapper $formMapper
     * @return FormBuilder
     */
    protected function getFileBuilder(FormMapper $formMapper)
    {
        // simulate an association ...
        $fieldDescription = $this->getFileAdmin()->getModelManager()->getNewFieldDescriptionInstance($this->getFileAdmin()->getClass(), 'file');
        $fieldDescription->setAssociationAdmin($this->getFileAdmin());
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'file',
            'type' => ClassMetadataInfo::MANY_TO_ONE,
        ));

        return $formMapper->create('fileId', 'sonata_type_model_list', array(
            'sonata_field_description' => $fieldDescription,
            'class' => $this->getFileAdmin()->getClass(),
            'model_manager' => $this->getFileAdmin()->getModelManager()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        $fileId = $block->getSetting('fileId', null);

        if (is_int($fileId)) {
            $file = $this->getFileRepository()->findOneBy(array('id' => $fileId));
            $block->setSetting('fileId', $file);
        }

    }

    /**
     * @return FileAdmin
     */
    protected function getFileAdmin()
    {
        if (!$this->fileAdmin) {
            $this->fileAdmin = $this->container->get('awaresoft.file.admin.file');
            return $this->fileAdmin;
        }

        return $this->fileAdmin;
    }

    /**
     * @return FileRepository
     */
    protected function getFileRepository()
    {
        return $this->getEntityManager()->getRepository('AwaresoftFileBundle:File');
    }

    /**
     * @param string $id
     * @return null|File
     */
    protected function findById($id)
    {
        return $this->getFileRepository()->find($id);
    }
}