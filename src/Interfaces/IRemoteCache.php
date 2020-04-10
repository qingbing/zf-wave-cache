<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\WaveCache\Interfaces;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    远程数据缓存接口
 *
 * Interface IRemoteCache
 * @package Zf\Cache\Interfaces
 */
interface IRemoteCache
{
    /**
     * @describe    获取远程缓存数据
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @describe    设置远程缓存数据
     *
     * @param string $key 缓存键
     * @param array $data 缓存内容
     * @param int $ttl 缓存有效时间
     * @return bool
     */
    public function set(string $key, array $data, int $ttl);

    /**
     * @describe    锁定，并发时只有一人能够拿到锁，防止缓存穿透
     *
     * @param string $lockKey
     *
     * @return bool
     */
    public function getLock(string $lockKey): bool;

    /**
     * @describe    锁用完后，删除锁
     *
     * @param string $lockKey
     *
     * @return bool
     */
    public function delLock(string $lockKey): bool;
}