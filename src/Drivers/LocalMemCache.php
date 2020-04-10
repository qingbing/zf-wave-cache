<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\WaveCache\Drivers;


use Zf\Helper\Abstracts\Component;
use Zf\WaveCache\Interfaces\ILocalCache;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    memcache 本地高速数据缓存
 *
 * Class LocalMemCache
 * @package Zf\WaveCache\Drivers
 */
class LocalMemCache extends Component implements ILocalCache
{
    /**
     * @describe    Redis 主机
     *
     * @var string
     */
    public $host = '127.0.0.1';
    /**
     * @describe    Redis 请求端口号
     *
     * @var int
     */
    public $port = 11211;

    /**
     * @describe    memcache 实例
     *
     * @var \Memcache
     */
    private $_memcache;

    /**
     * @describe    获取 memcache 实例
     *
     * @return \Memcache
     */
    public function getMemcache(): \Memcache
    {
        if (null === $this->_memcache) {
            $mem = new \Memcache();
            $mem->connect($this->host, $this->port);
            $this->_memcache = $mem;
        }
        return $this->_memcache;
    }

    /**
     * @describe    设置 memcache 实例
     *
     * @param \Memcache $memcache
     */
    public function setMemcache(\Memcache $memcache)
    {
        $this->_memcache = $memcache;
    }

    /**
     * @describe    获取高速缓存数据
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->getMemcache()->get($key);
    }

    /**
     * @describe    设置高速缓存数据
     *
     * @param string $key 缓存键
     * @param array $data 缓存内容
     * @param int $ttl 缓存有效时间
     * @return bool
     */
    public function set(string $key, array $data, int $ttl)
    {
        return $this->getMemcache()->set($key, $data, 0, $ttl);
    }

    /**
     * @describe    删除缓存
     *
     * @param string $key
     *
     * @return bool
     */
    public function del(string $key): bool
    {
        return $this->getMemcache()->delete($key);
    }
}