<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Tree;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Doctrine\Common\Collections\Collection;

/**
 * Main base class for attached or embedded tree leafs.
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
abstract class AbstractLeafs implements LeafsInterface
{
    use EntityTrait;

    /**
     * The leafs.
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", storeAs="dbRef", strategy="set", sort={"priority"="asc"})
     * @var Collection
     */
    private $items;

    public function getItems()
    {
        if (!$this->items) {
            $this->setItems(new ArrayCollection());
        }

        return $this->items;
    }

    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get a list of the names of all attached leafs.
     *
     * Will prefix the leaf name with its parents, unless the parent is
     * the root node.
     *
     * @return string
     */
    public function __toString()
    {
        $items = [];

        /* @var NodeInterface $item */
        foreach ($this->getItems() as $item) {
            $parent = $item->getParent(); $nameParts = [];
            while ($parent) {
                $nextParent = $parent->getParent();
                if ($nextParent) { $nameParts[] = $parent->getName(); }
                $parent = $nextParent;
            }
            $nameParts[] = $item->getName();
            $items[]     = join(' | ' , $nameParts);

        }

        $items = join(', ', $items);

        return $items;
    }
}