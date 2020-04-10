<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\WaveCache\Drivers;


use Zf\Helper\Abstracts\Component;
use Zf\WaveCache\Interfaces\IRemoteCache;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    redis远程缓存管理组件
 *
 * Class RemoteRedisCache
 * @package Zf\WaveCache\Drivers
 */
class RemoteRedisCache extends Component implements IRemoteCache
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
    public $port = 6379;
    /**
     * @describe    Redis 的连接密码，redis 服务器
     *
     * @var string
     */
    public $password = '';
    /**
     * @describe    Redis 的数据库
     *
     * @var int
     */
    public $dbIndex = 0;
    /**
     * @describe    Redis 连接实例
     *
     * @var \Redis
     */
    private $_redis;

    /**
     * @describe    设置已经连接的redis组件
     *
     * @param \Redis $redis
     */
    public function setRedis(\Redis $redis)
    {
        $this->_redis = $redis;
    }

    /**
     * @describe    获取redis连接实例
     *
     * @return \Redis
     */
    public function getRedis()
    {
        if (null === $this->_redis) {
            // redis连接实例
            $redis = new \Redis();
            $redis->connect($this->host, $this->port);
            if (null !== $this->password) {
                $redis->auth($this->password);
            }
            $redis->select($this->dbIndex);

            // 数据属性保存
            $this->_redis = $redis;
        }
        return $this->_redis;
    }

    /**
     * @describe    获取远程缓存数据
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return unserialize($this->getRedis()->get($key));
    }

    /**
     * @describe    设置远程缓存数据
     *
     * @param string $key 缓存键
     * @param array $data 缓存内容
     * @param int $ttl 缓存有效时间
     * @return bool
     */
    public function set(string $key, array $data, int $ttl)
    {
        return $this->getRedis()->setex($key, $ttl, serialize($data));
    }

    /**
     * @describe    锁定，并发时只有一人能够拿到锁，防止缓存穿透
     *
     * @param string $lockKey
     *
     * @return bool
     */
    public function getLock(string $lockKey): bool
    {
        // TODO: Implement lock() method.
        $result = $this->getRedis()->multi(\Redis::PIPELINE)
            ->incr($lockKey)
            ->expire($lockKey, 3)
            ->exec();
        return $result[0] <= 1;
    }

    /**
     * @describe    锁用完后，删除锁
     *
     * @param string $lockKey
     *
     * @return bool
     */
    public function delLock(string $lockKey): bool
    {
        return $this->getRedis()->del($lockKey);
    }
}