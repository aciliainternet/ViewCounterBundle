<?php
namespace Acilia\Bundle\ViewCounterBundle\Service;

use Acilia\Component\Memcached\Service\MemcachedService;
use Acilia\Bundle\ViewCounterBundle\Library\ViewCounter\ViewCounterInterface;

class ViewCounterService
{
    const REALTIME = false;
    const MEMCACHE_KEY = 'SIRE_VIEWS';

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
            $viewsSql = $this->getViewUpdate();
            $viewsStmt = str_replace(['%table%', '%field%', '%field_id%'], [$object->getViewCounterModel(), $object->getViewCounterField(), $object->getViewCounterFieldId()], $viewsSql);
            $viewsStmt->execute(['views' => 1, 'id' => $object->getViewCounterId()]);

        } else {
            // generate unique key for the visited element
            $key = sha1($object->getViewCounterModel() . ':' . $object->getViewCounterField() . ':' . $object->getViewCounterId());

            $entries = $this->memcache->get(self::MEMCACHE_KEY);
            if (!isset ($entries[$key])) {
                $entries[$key] = ['model' => $object->getViewCounterModel(), 'field' => $object->getViewCounterField(), 'field_id' => $object->getViewCounterFieldId(), 'id' => $object->getViewCounterId(), 'views' => 0];
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
        }
    }

    protected function getViewUpdate()
    {
        return 'UPDATE %table% SET %field% = %field% + :views WHERE %field_id% = :id';
    }
}
