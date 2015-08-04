<?php
namespace Acilia\Bundle\ViewCounterBundle\Library\ViewCounter;

interface ViewCounterInterface
{
    public function getViewCounterModel();

    public function getViewCounterField();

    public function getViewCounterFieldId();

    public function getViewCounterId();
}
