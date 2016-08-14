<?php

namespace Awaresoft\FileBundle\Admin;

use Awaresoft\FileBundle\Entity\File;
use Awaresoft\TreeBundle\Admin\AbstractTreeAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Class FileAdmin
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class FileAdmin extends AbstractTreeAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/file/file';

    /**
     * @inheritdoc
     */
    protected $multisite = true;

    /**
     * @inheritdoc
     */
    protected $titleField = 'name';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
    }

    /**
     * @inheritdoc
     *
     * @param File $object
     */
    public function prePersist($object)
    {
        if ($object->getParent() && $object->getParent()->getSite() !== $object->getSite()) {
            $object->setSite($object->getParent()->getSite());
        }
    }

    /**
     * @inheritdoc
     *
     * @param File $object
     */
    public function preUpdate($object)
    {
        $this->prePersist($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('name')
            ->add('site')
            ->add('parent')
            ->add('level')
            ->add('enabled')
            ->add('deletable')
            ->end();

        $showMapper
            ->with($this->trans('admin.admin.form.group.media'))
            ->add('media', 'html', [
                'template' => 'SonataAdminBundle:CRUD:show_image.html.twig',
            ])
            ->add('thumbnail', 'html', [
                'template' => 'SonataAdminBundle:CRUD:show_image.html.twig',
            ])
            ->end();

        $showMapper
            ->with($this->trans('admin.admin.form.group.classification'))
            ->add('tags')
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFieldsExtend(ListMapper $listMapper)
    {
        $listMapper
            ->add('site')
            ->add('media.name')
            ->add('thumbnail', 'html', ['template' => 'SonataAdminBundle:CRUD:list_image.html.twig'])
            ->add('media.content_type')
            ->add('enabled', null, ['editable' => true]);

        $editable = false;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $editable = true;
        }

        $listMapper
            ->add('deletable', null, ['editable' => $editable]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->prepareFilterMultisite($datagridMapper);

        $datagridMapper
            ->add('name')
            ->add('media.name')
            ->add('parent')
            ->add('level')
            ->add('enabled')
            ->add('deletable');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /**
         * @var File $object
         */
        $object = $this->getSubject();
        $maxDepthLevel = $this->prepareMaxDepthLevel('FILE');

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.main'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.file'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.thumbnail'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.classification'), ['class' => 'col-md-6'])->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('name')
            ->add('enabled', null, [
                'required' => false,
            ]);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->add('deletable', null, [
                    'required' => false,
                ]);
        }

        if ($this->hasSubject() && !$object->getId()) {
            $formMapper
                ->add('site', null, ['required' => true, 'read_only' => true]);
        }

        $this->addParentField($formMapper, $maxDepthLevel, $object->getSite());

        $formMapper
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.file'))
            ->add('media', 'sonata_media_type', [
                'cascade_validation' => true,
                'provider' => 'sonata.media.provider.file',
                'context' => 'files',
                'required' => false,
            ])
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.thumbnail'))
            ->add('thumbnail', 'sonata_media_type', [
                'cascade_validation' => true,
                'provider' => 'sonata.media.provider.image',
                'context' => 'files',
                'required' => false,
            ])
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.classification'))
            ->add('tags', 'sonata_type_model', [
                'property' => 'name',
                'multiple' => 'true',
                'required' => false,
            ])
            ->end();
    }
}
