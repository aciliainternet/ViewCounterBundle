<?php
namespace Acilia\Bundle\CountVisitsBundle\Service;

use Acilia\Component\Memcached\Service\MemcachedService;
use Acilia\Bundle\CountVisitsBundle\Library\CountVists;
use Acilia\Bundle\CountVisitsBundle\Library\CountVisits\VisitableInterface;

class CountVisitsService
{
    const REALTIME = false;
    const MEMCACHE_KEY = 'ACILIA_VISITS_';

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

    public function addVisit(VisitableInterface $object)
    {
        if ($this->options['realtime']) {
            $em = $this->doctrine->getManager();
            $object->addVisits(1);
            $em->persist($object);
            $em->flush();
        } else {
            $object_key = $object->getRepository() . '|SEP|' . $object->getId();
            $entries = $this->memcache->get(self::MEMCACHE_KEY);
            if (!isset ($entries[$object_key])) {
                $entries[$object_key] = 0;
            }
            $entries[$object_key] += 1;
            $this->memcache->set(self::MEMCACHE_KEY, $entries);
        }
    }

    public function processViews()
    {
        $entries = $this->memcache->get(self::MEMCACHE_KEY);

        foreach ($entries as $key => $visits) {
            list ($repo_name, $obj_id) = explode('|SEP|', $key);
            $object = $this->doctrine->getRepository($repo_name)->find($obj_id);
            if ($object) {
                $object->addVisits($visits);
            }
        }
        $this->doctrine->getManager()->flush();
    }
}
