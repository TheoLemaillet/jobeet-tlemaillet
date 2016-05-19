<?php

namespace Ens\TotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryAffiliate
 */
class CategoryAffiliate
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Ens\TotoBundle\Entity\Category
     */
    private $category;

    /**
     * @var \Ens\TotoBundle\Entity\Affiliate
     */
    private $affiliate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param \Ens\TotoBundle\Entity\Category $category
     * @return CategoryAffiliate
     */
    public function setCategory(\Ens\TotoBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Ens\TotoBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set affiliate
     *
     * @param \Ens\TotoBundle\Entity\Affiliate $affiliate
     * @return CategoryAffiliate
     */
    public function setAffiliate(\Ens\TotoBundle\Entity\Affiliate $affiliate = null)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * Get affiliate
     *
     * @return \Ens\TotoBundle\Entity\Affiliate 
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }
}
