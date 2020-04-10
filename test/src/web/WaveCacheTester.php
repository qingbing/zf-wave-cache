<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Test\Web;


use DebugBootstrap\Abstracts\Tester;
use Zf\Helper\Object;
use Zf\WaveCache\Drivers\LocalYacCache;
use Zf\WaveCache\Drivers\RemoteRedisCache;
use Zf\WaveCache\WaveCache;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    WaveCacheTester
 *
 * Class WaveCacheTester
 * @package Test\Web
 */
class WaveCacheTester extends Tester
{
    /**
     * @describe    执行函数
     *
     * @throws \ReflectionException
     * @throws \Zf\Helper\Exceptions\ClassException
     */
    public function run()
    {
        // 远端数据缓存组件，可参考redis，db等
        $remoteCache = Object::create([
            'class' => RemoteRedisCache::class,
            'host' => '172.16.37.145',
            'port' => 6379,
            'password' => 'iampassword',
            'dbIndex' => 0,
        ]);
        // 本地高速缓存，建议使用 Yac、memcache
        $localCache = Object::create([
            'class' => LocalYacCache::class,
            'prefix' => 'zf:',
        ]);

        // 二级缓存实例化
        $cache = Object::create([
            'class' => WaveCache::class,
            'remoteCache' => $remoteCache,
            'localCache' => $localCache,
        ]);

        $key = 'db:config';
        $callback = function () {
            return [
                'host' => "localhost",
                'time' => time(),
            ];
        };
        /* @var $cache WaveCache */
        // 设置缓存
//        $cache->set($key, $callback, 5);

        // 获取缓存
//        $data = $cache->get($key, $callback, 5);
//        var_dump($data);


        $status = $cache->del($key);

    }
}