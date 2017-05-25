<?php

	namespace Parcel\Cache;

	use Parcel\Cache\Storage\CacheStorageInterface;

	/**
	 * Class Cache
	 *
	 * @package Parcel\Cache
	 * @version 1.0.0
	 */
	class Cache {

		/**
		 * The default lifetime of a cached object.
		 *
		 * @var integer
		 */
		protected $lifetime;

		/**
		 * Class that is responsible for reading from the cache.
		 *
		 * @var CacheStorageInterface
		 */
		protected $storage;

		/**
		 * Cache constructor.
		 *
		 * @param CacheStorageInterface $storage  The handler for reading and writing data in the cache.
		 * @param integer               $lifetime The number of seconds that data should be stored in cache before it expires.
		 */
		public function __construct(CacheStorageInterface $storage, $lifetime = 86400) {
			$this->setStorage($storage);
			$this->setDefaultLifetime($lifetime);
		}

		/**
		 * Sets the handler for reading and writing to the cache.
		 *
		 * @param CacheStorageInterface $storage
		 */
		public function setStorage(CacheStorageInterface $storage) {
			$this->storage = $storage;
		}

		/**
		 * Set the default amount of time that data should be cached for.
		 *
		 * @param integer $lifetime The number of seconds that the data will be cached.
		 */
		public function setDefaultLifetime($lifetime = 86400) {
			$this->lifetime = intval($lifetime);

			if ($this->lifetime < 0) {
				$this->lifetime = 0;
			}
		}

		/**
		 * Returns the default lifetime of cached data.
		 *
		 * @return integer
		 */
		public function getDefaultLifetime() {
			return $this->lifetime;
		}

		/**
		 * Retrieve data that has been cached.
		 *
		 * This method will return the data, regardless of whether it is expired.
		 *
		 * @param string $key
		 *
		 * @return CacheData|false
		 */
		public function read($key) {
			if (!$this->storage->exists($key)) {
				return false;
			}

			if (($data = $this->storage->read($key)) === null) {
				return false;
			}

			return $data;
		}

		/**
		 * Write data to the cache.
		 *
		 * @param string       $key
		 * @param mixed        $data
		 * @param integer|null $lifetime
		 *
		 * @return CacheData|false
		 */
		public function write($key, $data, $lifetime = null) {
			if (!is_string($key)) {
				return false;
			}

			if (!is_int($lifetime) || $lifetime < 0) {
				$lifetime = $this->lifetime;
			}

			$cacheData = new CacheData($key, $data, $lifetime);

			return $this->cache($cacheData);
		}

		/**
		 * Save the supplied data to the cache.
		 *
		 * @param CacheData $data
		 *
		 * @return CacheData|false
		 */
		public function cache(CacheData $data) {
			if ($data->getKey() === null) {
				return false;
			}

			$data->updateExpirationTime();

			if ($this->storage->write($data) === false) {
				return false;
			}

			return $data;
		}

		/**
		 * Deletes the data associated with the unique key from the cache.
		 *
		 * @param string $key Unique identifier for the cached data.
		 *
		 * @return boolean
		 */
		public function delete($key) {
			if (!$this->storage->exists($key)) {
				return true;
			}

			return $this->storage->delete($key);
		}

		/**
		 * Check whether the cache already has data for the supplied key.
		 *
		 * This will return true even if the data has expired.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function exists($key) {
			return $this->storage->exists($key);
		}

		/**
		 * Check whether the cached data has expired.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function isExpired($key) {
			return $this->storage->isExpired($key);
		}
	}
