<?php

namespace Awaresoft\FileBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Awaresoft\FileBundle\Entity\File;

/**
 * Class LoadFileDevData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadFileDevData extends AwaresoftAbstractFixture
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
        return array('dev');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->createFiles($manager);
    }

    protected function createFiles(ObjectManager $manager)
    {
        $faker = $this->getFaker();
        $media1 = $this->getReference('sonata-media-1');
        $media2 = $this->getReference('sonata-media-2');

        $root = new File();
        $root
            ->setEnabled(true)
            ->setName('root')
            ->setSite($this->getReference('page-site'));

        $manager->persist($root);

        $file = new File();
        $file
            ->setEnabled(true)
            ->setName($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($root)
            ->setMedia($media1);

        $manager->persist($file);

        $file2 = new File();
        $file2
            ->setEnabled(true)
            ->setName($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($file)
            ->setMedia($media2);

        $manager->persist($file2);

        $file3 = new File();
        $file3
            ->setEnabled(true)
            ->setName($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($file2)
            ->setMedia($media1);

        $manager->persist($file3);

        $file4 = new File();
        $file4
            ->setEnabled(true)
            ->setName($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($root)
            ->setMedia($media2);

        $manager->persist($file4);

        $manager->flush();
    }
}
