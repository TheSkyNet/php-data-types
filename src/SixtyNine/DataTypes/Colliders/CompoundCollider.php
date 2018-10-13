<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

class CompoundCollider extends Collider
{
    /** @var array */
    protected $children = [];

    public function __construct(array $colliders = [])
    {
        foreach ($colliders as $collider) {
            if ($collider instanceof ColliderInterface) {
                $this->addCollider($collider);
            }
        }
    }

    public function addCollider(ColliderInterface $collider)
    {
        $this->children[] = $collider;
    }

    public function getBoundingBox(): Box
    {
        $minX = $minY = PHP_INT_MAX;
        $maxX = $maxY = -PHP_INT_MAX;

        /** @var ColliderInterface $collider */
        foreach ($this->children as $collider) {
            $bb = $collider->getBoundingBox();
            $minX = min($minX, $bb->getLeft());
            $minY = min($minY, $bb->getTop());
            $maxX = max($maxX, $bb->getRight());
            $maxY = max($maxY, $bb->getBottom());
        }

        return new Box($minX, $minY, $maxX - $minX, $maxY - $minY);
    }

    public function collidesWithPoint(Vector $p): bool
    {
        /** @var ColliderInterface $collider */
        foreach ($this->children as $collider) {
            if ($collider->collidesWithPoint($p)) {
                return true;
            }
        }
        return false;
    }

    public function collidesWithCollider(ColliderInterface $collider): bool
    {
        /** @var ColliderInterface $c */
        foreach ($this->children as $c) {
            if ($c->collidesWithCollider($collider)) {
                return true;
            }
        }
        return false;
    }
}
