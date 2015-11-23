<?php
namespace Acilia\Bundle\ViewCounterBundle\Service;

use Acilia\Bundle\ViewCounterBundle\Entity\ViewCounter;
use Acilia\Component\Memcached\Service\MemcachedService;
use Acilia\Bundle\ViewCounterBundle\Library\ViewCounter\ViewCounterInterface;

class ViewCounterService
{
    const REALTIME = false;
    const MEMCACHE_KEY = 'ACILIA_VIEW_COUNTER';

    protected $doctrine;
    protected $memcache;
    protected $options;

    public function __construct($doctrine, MemcachedService $memcache, $options)
    {
        $this->doctrine = $doctrine;
        $this->memcache = $memcache;
        $this->options = $options;
        if (!isset ($options['realtime'])) {
            $this->options['realtime'] = self::REALTIME;
        }
    }

    public function addView(ViewCounterInterface $object)
    {
        if ($this->options['realtime']) {

            $em = $this->doctrine->getManager();
            $visit = $em->getRepository('AciliaViewCounterBundle:ViewCounter')->findOneBy(['viewModel' => $object->getViewCounterModel(), 'viewModelId' => $object->getViewCounterId(), 'viewDate' => new \DateTime("now")]);
            if ($visit) {
                $visit->setViews($visit->getViews() + 1);
            } else {
                $visit = new ViewCounter();
                $visit->setViewModel($object->getViewCounterModel());
                $visit->setViewModelId($object->getViewCounterId());
                $visit->setViews(1);
                $visit->setViewDate(new \DateTime("now"));
            }
            $em->persist($visit);
            $em->flush();

            $viewsSql = $this->getViewUpdate();

            $viewsStmt = str_replace(['%table%', '%field%', '%field_id%'], [$object->getViewCounterModel(), $object->getViewCounterField(), $object->getViewCounterFieldId()], $viewsSql);
            $viewsStmt = $this->doctrine->getManager()->getConnection()->prepare($viewsStmt);
            $viewsStmt->execute(['views' => 1, 'id' => $object->getViewCounterId()]);

        } else {
            // generate unique key for the visited element
            $key = sha1($object->getViewCounterModel() . ':' . $object->getViewCounterField() . ':' . $object->getViewCounterId());

            $entries = $this->memcache->get(self::MEMCACHE_KEY);
            if (!isset ($entries[$key])) {
                $entries[$key] = ['model' => $object->getViewCounterModel(), 'field' => $object->getViewCounterField(), 'field_id' => $object->getViewCounterFieldId(), 'id' => $object->getViewCounterId(), 'views' => 1, 'date' => date("Y-m-d")];
            } else {
                $entries[$key]['views'] += 1;
            }
            $this->memcache->set(self::MEMCACHE_KEY, $entries);
        }
    }

    public function processViews()
    {
        $entries = $this->memcache->get(self::MEMCACHE_KEY);
        $viewsSql = $this->getViewUpdate();
        foreach ($entries as $key => $value) {
            $viewsStmt = str_replace(['%table%', '%field%', '%field_id%'], [$value['model'], $value['field'], $value['field_id']], $viewsSql);
            try {
                $viewsStmt = $this->doctrine->getManager()->getConnection()->prepare($viewsStmt);
                $viewsStmt->execute(['views' => $value['views'], 'id' => $value['id']]);
            } catch (Exception $e) {}

            try {
                $em = $this->doctrine->getManager();
                $visit = $em->getRepository('AciliaViewCounterBundle:ViewCounter')->findOneBy(['viewModel' => $value['model'], 'viewModelId' => $value['id'], 'viewDate' => new \DateTime($value['date'])]);
                if ($visit) {
                    $visit->setViews((integer) $visit->getViews() + (integer) $value['views']);
                } else {
                    $visit = new ViewCounter();
                    $visit->setViewModel($value['model']);
                    $visit->setViewModelId($value['id']);
                    $visit->setViews($value['views']);
                    $visit->setViewDate(new \DateTime($value['date']));
                }
                $em->persist($visit);
                $em->flush();
            } catch (Exception $e) {}
        }
        // clear processed views
        $this->memcache->set(self::MEMCACHE_KEY, [], 0);
    }


    protected function getViewUpdate()
    {
        return 'UPDATE %table% SET %field% = COALESCE(%field%, 0) + :views WHERE %field_id% = :id';
    }
}
