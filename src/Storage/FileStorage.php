<?php

	namespace Parcel\Cache\Storage;

	use Parcel\Cache\CacheData;

	/**
	 * Class FileStorage
	 *
	 * @package Parcel\Cache\Storage
	 * @version 1.0.0
	 */
	class FileStorage implements CacheStorageInterface {

		protected $folder;

		/**
		 * FileStorage constructor.
		 *
		 * @param string $folder The folder on the filesystem where the cache files will be stored.
		 */
		public function __construct($folder) {
			$this->folder = rtrim($folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}

		/**
		 * Make the cache folder.
		 *
		 * @param $folder
		 *
		 * @return bool
		 */
		public function makeFolder($folder) {
			if (!is_dir($folder)) {
				if (!mkdir($folder, 0777, true)) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Returns a value from the cache.
		 *
		 * @param string $key
		 *
		 * @return mixed|null
		 * @throws \Exception
		 */
		public function read($key) {
			if (!$this->exists($key)) {
				return null;
			}

			if (($contents = file_get_contents($this->folder . md5($key))) === false) {
				throw new \Exception("Could not read the cache file.");
			}

			if (($data = unserialize($contents)) === false) {
				throw new \Exception("Could not unserialize the cached value.");
			}

			return $data;
		}

		/**
		 * Check whether a value exists in cache.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function exists($key) {
			return file_exists($this->folder . md5($key));
		}

		/**
		 * Check whether a value has expired.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function isExpired($key) {
			if (($data = $this->read($key)) === false) {
				return true;
			}

			return $data->isExpired();
		}

		/**
		 * Writes a value to the cache.
		 *
		 * @param CacheData $data
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public function write(CacheData $data) {
			if (!$this->makeFolder($this->folder)) {
				throw new \Exception("Could not make the cache folder.");
			}

			$filename = md5($data->getKey());

			if (!file_put_contents($this->folder . $filename, serialize($data))) {
				throw new \Exception("Could not create the cache file.");
			}

			return true;
		}

		/**
		 * Removes a value that has been cached.
		 *
		 * @param CacheData $data
		 *
		 * @return bool
		 */
		public function erase(CacheData $data) {
			return $this->delete($data->getKey());
		}

		/**
		 * Deletes a value that has been cached.
		 *
		 * @param string $key
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public function delete($key) {
			if ($this->exists($key)) {
				if (!unlink($this->folder . md5($key))) {
					throw new \Exception("Could not delete cache file.");
				}
			}

			return true;
		}
	}
