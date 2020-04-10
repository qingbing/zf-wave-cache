<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\WaveCache\Interfaces;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    本地高速数据缓存接口
 *
 * Interface ILocalCache
 * @package Zf\Cache\Interfaces
 */
interface ILocalCache
{
    /**
     * @describe    获取高速缓存数据
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @describe    设置高速缓存数据
     *
     * @param string $key 缓存键
     * @param array $data 缓存内容
     * @param int $ttl 缓存有效时间
     * @return bool
     */
    public function set(string $key, array $data, int $ttl);

    /**
     * @describe    删除缓存
     *
     * @param string $key
     *
     * @return bool
     */
    public function del(string $key): bool;
}