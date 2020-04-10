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
 * @describe    Yac本地高速数据缓存
 *
 * Class LocalYacCache
 * @package Zf\WaveCache\Drivers
 */
class LocalYacCache extends Component implements ILocalCache
{
    /**
     * @describe    Yac 实例前缀
     *
     * @var string
     */
    public $prefix = "ZF:";
    /**
     * @describe    Yac 实例
     *
     * @var \Yac
     */
    private $_yac;

    /**
     * @describe    设置 Yac 实例
     *
     * @param \Yac $yac
     */
    public function setYac(\Yac $yac)
    {
        $this->_yac = $yac;
    }

    /**
     * @describe    获取 Yac 实例
     *
     * @return \Yac
     */
    public function getYac()
    {
        if (null === $this->_yac) {
            $this->_yac = new \Yac($this->prefix);
        }
        return $this->_yac;
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
        $result = $this->getYac()->get($key);
        return $result ?? null;
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
        return $this->getYac()->set($key, $data, $ttl);
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
        return $this->getYac()->delete($key);
    }
}