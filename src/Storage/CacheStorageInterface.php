<?php

	namespace Parcel\Cache\Storage;

	use Parcel\Cache\CacheData;

	interface CacheStorageInterface {
		/**
		 * Retrieves the cached data.
		 *
		 * @param string $key The unique identifier for the cached data.
		 *
		 * @return CacheData
		 */
		public function read($key);

		/**
		 * Check whether there is any data cached.
		 *
		 * @param string $key
		 *
		 * @return boolean
		 */
		public function exists($key);

		/**
		 * Check whether the data has expired.
		 *
		 * @param string $key
		 *
		 * @return boolean
		 */
		public function isExpired($key);

		/**
		 * Delete the cached data.
		 *
		 * @param $key
		 *
		 * @return boolean
		 */
		public function delete($key);

		/**
		 * Deletes the cached data.
		 *
		 * @param CacheData $data
		 *
		 * @return boolean
		 */
		public function erase(CacheData $data);

		/**
		 * Saves the data to the cache.
		 *
		 * @param CacheData $data
		 *
		 * @return CacheData|false
		 */
		public function write(CacheData $data);
	}
