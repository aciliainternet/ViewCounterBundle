<?php
namespace Acilia\Bundle\ViewCounterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="acilia_view_counter", options={"collate"="utf8_unicode_ci", "charset"="utf8", "engine"="InnoDB"}, indexes={@ORM\Index(name="idx_acilia_view_counter", columns={"view_model", "view_model_id", "view_date"})})
 */
class ViewCounter
{
    /**
     * @ORM\Column(name="view_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="view_model", type="string", length=32)
     */
    protected $viewModel;

    /**
     * @ORM\Column(name="view_model_id", type="integer")
     */
    protected $viewModelId;

    /**
     * @ORM\Column(name="view_views", type="integer")
     */
    protected $views;

    /**
     * @ORM\Column(name="view_date", type="date", nullable=false)
     */
    protected $viewDate;

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
     * Set viewModel
     *
     * @param string $viewModel
     * @return ViewCounter
     */
    public function setViewModel($viewModel)
    {
        $this->viewModel = $viewModel;

        return $this;
    }

    /**
     * Get viewModel
     *
     * @return string
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Set viewModelId
     *
     * @param integer $viewModelId
     * @return ViewCounter
     */
    public function setViewModelId($viewModelId)
    {
        $this->viewModelId = $viewModelId;

        return $this;
    }

    /**
     * Get viewModelId
     *
     * @return integer
     */
    public function getViewModelId()
    {
        return $this->viewModelId;
    }

    /**
     * Set viewViews
     *
     * @param integer $viewViews
     * @return ViewCounter
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set viewDate
     *
     * @param \DateTime $viewDate
     * @return ViewCounter
     */
    public function setViewDate($viewDate)
    {
        $this->viewDate = $viewDate;

        return $this;
    }

    /**
     * Get viewDate
     *
     * @return \DateTime
     */
    public function getViewDate()
    {
        return $this->viewDate;
    }
}
