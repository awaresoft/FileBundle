<?php

namespace Awaresoft\FileBundle\Entity;

use Awaresoft\Sonata\PageBundle\Entity\Site;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Awaresoft\TreeBundle\Entity\AbstractTreeNode;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="Awaresoft\FileBundle\Entity\FileRepository")
 * @Gedmo\Tree(type="nested")
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class File extends AbstractTreeNode
{
    const TREE_MAIN_COLUMN = 'name';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="tree_left")
     *
     * @Gedmo\TreeLeft
     *
     * @var int
     */
    protected $left;

    /**
     * @ORM\Column(type="integer", name="tree_right")
     *
     * @Gedmo\TreeRight
     *
     * @var int
     */
    protected $right;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Gedmo\TreeParent
     *
     * @var File
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", nullable=true, name="tree_root")
     *
     * @Gedmo\TreeRoot
     *
     * @var File
     */
    protected $root;

    /**
     * @ORM\Column(name="tree_level", type="integer")
     *
     * @Gedmo\TreeLevel
     *
     * @var int
     */
    protected $level;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="parent")
     * @ORM\OrderBy({"left" = "ASC"})
     *
     * @var File[]
     */
    protected $children;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\PageBundle\Entity\Site")
     *
     * @var Site
     */
    protected $site;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     *
     * @var MediaInterface
     */
    protected $media;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     *
     * @var MediaInterface
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $deletable;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Awaresoft\Sonata\ClassificationBundle\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(name="file_tags",
     *      joinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $tags;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->deletable = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param int $left
     *
     * @return $this
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     *
     * @return $this
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * @return File
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param File $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent ? true : false;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     *
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return string
     *
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Awaresoft\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param MediaInterface $media
     *
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return MediaInterface
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param MediaInterface $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @param bool $deletable
     *
     * @return $this
     */
    public function setDeletable($deletable)
    {
        $this->deletable = $deletable;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param TagInterface $tags
     */
    public function addTags(TagInterface $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param $tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getTitleFieldName()
    {
        return self::TREE_MAIN_COLUMN;
    }
}