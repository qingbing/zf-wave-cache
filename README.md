# zf-wave-cache
二级缓存实现，通过yac或memcache作为一级，redis或db作为二级实现

# 简介
- WaveCache缓存类提供了二级缓存的封装
- 可以自定义一级和二级缓存组件，组件默认封装了Yac和Memcache作为本地缓存，redis作为远端缓存
- 一级（本地高速）缓存组件需要继承 "\Zf\WaveCache\Interfaces\ILocalCache"
- 二级（远端缓存）缓存组件需要继承 "\Zf\WaveCache\Interfaces\IRemoteCache"

# 使用范例
```php

// Redis 远端数据缓存组件，可参考redis，db等
$remoteCache = Object::create([
    'class' => RemoteRedisCache::class,
    'host' => '172.16.37.145',
    'port' => 6379,
    'password' => 'iampassword',
    'dbIndex' => 0,
]);

// Yac 本地高速缓存
$localCache = Object::create([
    'class' => LocalYacCache::class,
    'prefix' => 'zf:',
]);
// memcache 本地高速缓存
$localCache = Object::create([
    'class' => LocalMemCache::class,
    'host' => '172.16.37.128',
    'port' => 10000,
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
$cache->set($key, $callback, 5);

// 获取缓存
$data = $cache->get($key, $callback, 5);
var_dump($data);

$status = $cache->del($key);
var_dump($status);
```