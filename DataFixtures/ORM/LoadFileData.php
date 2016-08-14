<?php

namespace Awaresoft\FileBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Awaresoft\SettingBundle\Entity\Setting;
use Awaresoft\SettingBundle\Entity\SettingHasField;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadFileData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadFileData extends AwaresoftAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 13;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return array('dev', 'prod');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->loadSettings($manager);
    }

    protected function loadSettings(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting
            ->setName('FILE')
            ->setEnabled(false)
            ->setHidden(true)
            ->setInfo('File global parameters.');
        $manager->persist($setting);

        $settingField = new SettingHasField();
        $settingField->setSetting($setting);
        $settingField->setName('MAX_DEPTH');
        $settingField->setValue('1');
        $settingField->setInfo('Set max depth for file items. If you want to specific max depth for selected file, please add option MAX_DEPTH_[FILE_NAME]');
        $settingField->setEnabled(false);
        $manager->persist($settingField);

        $manager->flush();
    }
}
