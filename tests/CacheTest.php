<?php

	namespace Parcel\Cache\Tests;

	use Parcel\Cache\Cache;
	use Parcel\Cache\Storage\InMemoryStorage;
	use PHPUnit\Framework\TestCase;

	class CacheTest extends TestCase
	{
		/**
		 * Returns a mock cache service that uses an array which is shared
		 * between both the cache reader and writer.
		 *
		 * @return Cache
		 */
		protected function getMockedCache() {
			return new Cache(new InMemoryStorage());
		}

		/**
		 * Check to make sure that we can't access non-existent data.
		 */
		public function testCacheDataDoesNotExist() {
			$cache = $this->getMockedCache();
			$this->assertFalse($cache->exists('test'));
		}

		/**
		 * Check to make sure that we can write data to the cache.
		 */
		public function testCacheCanWrite() {
			$cache = $this->getMockedCache();

			$return = $cache->write('test', ['One', 'Two', 'Three']);

			$this->assertTrue(is_object($return));
			$this->assertTrue($cache->exists('test'));
		}

		/**
		 * Check to make sure that we can read data from the cache.
		 */
		public function testCacheCanRead() {
			$cache = $this->getMockedCache();

			$cache->write('test', ['One', 'Two', 'Three']);

			$data = $cache->read('test');

			$this->assertTrue(is_object($data));
			$this->assertCount(3, $data->getData());
		}

		/**
		 * Check to make sure that data can expire.
		 */
		public function testCacheCanExpire() {
			$cache = $this->getMockedCache();
			$data  = $cache->write('test', ['One', 'Two', 'Three'], 1);

			$this->assertEquals(86400, $cache->getDefaultLifetime());
			$this->assertEquals(1, $data->getLifetime());

			sleep(2);

			$this->assertTrue($cache->isExpired('test'));

			$data = $cache->read('test');

			$this->assertTrue($data->isExpired());
		}

		/**
		 * Check to make sure that we can change the default lifetime.
		 */
		public function testCacheSetGetDefaultLifetime() {
			$cache = $this->getMockedCache();
			$cache->setDefaultLifetime(123);

			$this->assertEquals(123, $cache->getDefaultLifetime());
		}

		/**
		 * Check to make sure that we can update the expiration time of the data.
		 */
		public function testCacheCanUpdateExpirationTime() {
			$cache = $this->getMockedCache();
			$data  = $cache->write('test', ['One', 'Two', 'Three'], 1);

			sleep(2);

			$this->assertTrue($data->isExpired());

			$data->updateExpirationTime();

			$this->assertFalse($data->isExpired());
		}

		/**
		 * Check to make sure that the default lifetime is transferred over to the cache data object.
		 */
		public function testCacheTransfersDefaultLifetime() {
			$cache = $this->getMockedCache();
			$cache->setDefaultLifetime(123);

			$data = $cache->write('test', ['One', 'Two', 'Three']);

			$this->assertEquals(123, $data->getLifetime());
		}

		/**
		 * Check to make sure that we can delete data from the cache.
		 */
		public function testCacheCanDeleteData() {
			$cache = $this->getMockedCache();

			$cache->write('test', ['One', 'Two', 'Three']);

			$this->assertTrue($cache->exists('test'));
			$this->assertTrue($cache->delete('test'));
			$this->assertFalse($cache->exists('test'));
		}
	}
