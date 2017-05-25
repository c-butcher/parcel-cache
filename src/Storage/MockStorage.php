<?php

	namespace Parcel\Cache\Storage;

	use Parcel\Cache\CacheData;

	/**
	 * Class MockStorage
	 *
	 * This class simulates data being stored in the cache for testing purposes.
	 *
	 * @package Parcel\Cache\Storage
	 */
	class MockStorage implements CacheStorageInterface {
		protected $_data;

		public function __construct(array $storage = array()) {
			$this->_data = $storage;
		}

		/**
		 * Check whether the cached data exists.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function exists($key) {
			return isset($this->_data[$key]);
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

			return $this->_data[$key];
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		public function delete($key) {
			if (isset($this->_data[$key])) {
				unset($this->_data[$key]);
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

			$this->_data[$data->getKey()] = $data;

			return $this->_data[$data->getKey()];
		}
	}
