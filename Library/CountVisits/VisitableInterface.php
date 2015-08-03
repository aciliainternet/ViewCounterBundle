<?php
namespace Acilia\Bundle\CountVisitsBundle\Library\CountVisits;

interface VisitableInterface
{
    public function getId();

    public function getRepository();

    public function addVisits($visits);
}
