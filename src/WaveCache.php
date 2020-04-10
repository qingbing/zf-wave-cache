<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\WaveCache;


use Zf\Helper\Abstracts\Component;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    二级缓存，支持SLB
 *
 * Class WaveCache
 * @package Zf\WaveCache
 */
final class WaveCache extends Component
{
    /**
     * @describe    describe
     *
     * @var int
     */
    public $emptyExpress = -1000;
    /**
     * @describe    远端缓存组件
     *
     * @var \Zf\WaveCache\Interfaces\IRemoteCache
     */
    public $remoteCache;
    /**
     * @describe    本地高速数据缓存组件
     *
     * @var \Zf\WaveCache\Interfaces\ILocalCache
     */
    public $localCache;
    /**
     * @describe    远端缓存的时间倍数
     *
     * @var int
     */
    public $remoteSaveTimes = 3;
    /**
     * @describe    缓存锁前缀
     *
     * @var string
     */
    public $lockPrefix = 'wave:lock:';

    /**
     * @describe    数据是否在有效期内
     *
     * @param $res
     * @param $time
     *
     * @return bool
     */
    protected function isExpire($res, $time)
    {
        return $res['expireAt'] > $time;
    }

    /**
     * @describe    获取缓存，如果不存在，有可能会使用 $callback 重新创建
     *
     * @param $key
     * @param callable $callback
     * @param int $ttl
     *
     * @return mixed
     */
    public function get($key, callable $callback, $ttl = 600)
    {
        $time = time();
        // 优先使用本地缓存
        $localRes = $this->localCache->get($key);

        //本地读取到
        if (null !== $localRes) {
            if ($this->isExpire($localRes, $time)) {
                // 未过期
                return $localRes['data'];
            }
        }
        // 读取远端缓存
        $remoteRes = $this->remoteCache->get($key);

        if (null === $remoteRes) {
            // 远程缓存不存在
            return $this->set($key, $callback, $ttl);
        }
        // 未过期，说明在其他端更新了缓存
        if ($this->isExpire($remoteRes, $time)) {
            $this->localCache->set($key, $remoteRes, $ttl);
            return $remoteRes['data'];
        }
        // 远端数据无效，去占锁
        $lockKey = $this->lockPrefix . $key;
        if (!$this->remoteCache->getLock($lockKey)) {
            // 没有拿到锁，直接返回远端缓存结果，下次再来时，远端就是占锁的客户端已经更新的数据了
            return $remoteRes['data'];
        }

        // 设置缓存，包含本地和远端
        $result = $this->set($key, $callback, $ttl);
        // 删除远端锁
        $this->remoteCache->delLock($lockKey);
        // 返回结果
        return $result;
    }

    /**
     * @describe    设置缓存
     *
     * @param string $key
     * @param callable $callback 通过回调函数的返回值作为缓存值，在生成过程中，回调数据可以从任何地方获取
     * @param int $ttl
     *
     * @return mixed
     */
    public function set(string $key, callable $callback, int $ttl = 600)
    {
        $result = call_user_func($callback);
        $data = [
            'data' => empty($result) ? $this->emptyExpress : $result,
            'expireAt' => time() + $ttl,
            'ttl' => $ttl,
        ];

        // 设置本地缓存
        $this->localCache->set($key, $data, $ttl);
        // 设置远端缓存
        $this->remoteCache->set($key, $data, $ttl * $this->remoteSaveTimes);
        // 返回缓存结果
        return $result;
    }

    /**
     * @describe    删除缓存
     *
     * @param $key
     *
     * @return bool
     */
    public function del($key)
    {
        // 删除本地缓存
        $this->localCache->del($key);
        // 获取远端缓存
        $remoteRes = $this->remoteCache->get($key);
        if (null === $remoteRes || !isset($remoteRes['expireAt'])) {
            // 远端缓存不存在，直接返回
            return true;
        }
        // 设置远端缓存数据无效
        $remoteRes['expireAt'] = 0;
        $ttl = isset($remoteRes['ttl']) ? (int)$remoteRes['ttl'] : 0;
        $this->remoteCache->set($key, $remoteRes, $this->remoteSaveTimes * $ttl);

        return true;
    }
}