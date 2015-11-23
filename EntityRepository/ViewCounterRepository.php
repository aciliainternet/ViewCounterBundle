<?php
namespace Acilia\Bundle\ViewCounterBundle\EntityRepository;

use Acilia\Bundle\ViewCounterBundle\Entity\ViewCounter;
use Doctrine\ORM\EntityRepository;

class ViewCounterRepository extends EntityRepository
{
    public function fetchVisits($viewModel, $viewModelId, $viewDate)
    {
        $date = date_create_from_format("Y-m-d", $viewDate);
        $query = $this->createQueryBuilder('v')
                    ->where('v.viewModel = :viewModel')->setParameter(':viewModel', $viewModel)
                    ->andWhere('v.viewModelId = :viewModelId')->setParameter(':viewModelId', $viewModelId)
                    ->andWhere('v.viewDate = :viewDate')->setParameter(':viewDate', $date)
            ->getQuery();
        return $query->getArrayResult();
    }
}
