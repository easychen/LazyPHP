<?php

namespace Tonic;

/**
 * Cache resource metadata between invocations
 *
 * This class writes the resource metadata to MC on SAE for reading in a later request.
 */
class MetadataCacheMC implements MetadataCache
{
    private $cacheName = 'tonicCache';

    public function __construct($cacheName = null)
    {
        if ($cacheName) 
        {
            $this->cacheName = $cacheName;
        }

        $this->cacheTimeKey = $this->cacheName.'-time';

        if( defined('SAE_APPNAME') )
            $this->mc = memcache_init();
        else
        {
            $this->mc = new Memcache;
            $this->mc->connect('localhost', 11211) or die ("Could not connect");
        }
    }

    /**
     * Is there already cache file
     * @return boolean
     */
    public function isCached()
    {
        return $this->mc->get($this->cacheName) !== false ;
    }

    /**
     * Load the resource metadata from disk
     * @return str[]
     */
    public function load()
    {
        return $this->mc->get($this->cacheName);
    }

    /**
     * Save resource metadata to disk
     * @param  str[]   $resources Resource metadata
     * @return boolean
     */
    public function save($resources)
    {
        $this->mc->set($this->cacheTimeKey , time() , false , 600 );
        return $this->mc->set($this->cacheName, $resources , false , 600 );
    }

    public function clear()
    {
        $this->mc->delete($this->cacheName);
        $this->mc->delete($this->cacheTimeKey);

    }

    public function __toString()
    {
        //$info = apc_cache_info('user');
        return 'Metadata for '.count($this->load()).' resources stored in MC at '.date('r', $this->mc->get($this->cacheTimeKey));
    }

}
