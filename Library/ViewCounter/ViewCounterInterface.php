<?php
namespace Acilia\Bundle\ViewCounterBundle\Library\ViewCounter;

interface ViewCounterInterface
{
    const VIEW_MODEL_SERIE = 'serie';
    const VIEW_MODEL_MOVIE = 'movie';
    const VIEW_MODEL_CLIP = 'clip';

    public function getViewCounterModel();

    public function getViewCounterField();

    public function getViewCounterFieldId();

    public function getViewCounterId();
}
