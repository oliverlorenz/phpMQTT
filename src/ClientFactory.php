<?php

namespace oliverlorenz\reactphpmqtt;

use oliverlorenz\reactphpmqtt\protocol\Version;
use Psr\Log\LoggerInterface;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\DnsConnector;
use React\Socket\SecureConnector;
use React\Socket\TcpConnector;

class ClientFactory
{
    public static function createClient(Version $version, $resolverIp = '8.8.8.8', LoggerInterface $logger = null)
    {
        $loop = EventLoopFactory::create();
        $connector = self::createDnsConnector($resolverIp, $loop);

        return new MqttClient($loop, $connector, $version, $logger);
    }

    public static function createSecureClient(Version $version, $resolverIp = '8.8.8.8', LoggerInterface $logger = null)
    {
        $loop = EventLoopFactory::create();
        $connector = self::createDnsConnector($resolverIp, $loop);
        $secureConnector = new SecureConnector($connector, $loop);

        return new MqttClient($loop, $secureConnector, $version, $logger);
    }

    private static function createDnsConnector($resolverIp, $loop)
    {
        $dnsResolverFactory = new DnsResolverFactory();
        $resolver = $dnsResolverFactory->createCached($resolverIp, $loop);

        return new DnsConnector(new TcpConnector($loop), $resolver);
    }
}
