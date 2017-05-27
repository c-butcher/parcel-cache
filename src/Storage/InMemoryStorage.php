<?php

	namespace Parcel\Cache\Storage;

	use Parcel\Cache\CacheData;

	/**
	 * Class InMemoryStorage
	 *
	 * @package Parcel\Cache\Storage
	 * @version 1.0.0
	 */
	class InMemoryStorage implements CacheStorageInterface {
		/**
		 * The cached data.
		 *
		 * @var array
		 */
		protected $_cache;

		/**
		 * InMemoryStorage constructor.
		 *
		 * @param array $storage
		 */
		public function __construct(array $storage = array()) {
			$this->_cache = $storage;
		}

		/**
		 * Check whether the cached data exists.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function exists($key) {
			return isset($this->_cache[$key]);
		}

		/**
		 * Check whether the cached data has expired.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function isExpired($key) {
			if (($data = $this->read($key)) === null) {
				return true;
			}

			return $data->isExpired();
		}

		/**
		 * @param string $key
		 *
		 * @return CacheData
		 */
		public function read($key) {
			if (!$this->exists($key)) {
				return null;
			}

			return $this->_cache[$key];
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		public function delete($key) {
			if (isset($this->_cache[$key])) {
				unset($this->_cache[$key]);
			}

			return true;
		}

		/**
		 * @param CacheData $data
		 *
		 * @return bool
		 */
		public function erase(CacheData $data) {
			return $this->delete($data->getKey());
		}

		/**
		 * @param CacheData $data
		 *
		 * @return bool|mixed
		 */
		public function write(CacheData $data) {
			if ($data->getKey() == null) {
				return false;
			}

			$this->_cache[$data->getKey()] = $data;

			return $this->_cache[$data->getKey()];
		}
	}
