<?php

	namespace Parcel\Cache;

	/**
	 * Class CacheData
	 *
	 * @package Parcel\Cache
	 * @version 1.0.0
	 */
	class CacheData {

		/**
		 * Unique identifier for the content being cached.
		 *
		 * @var string
		 */
		protected $key;

		/**
		 * The data that is being cached.
		 *
		 * @var mixed
		 */
		protected $data;

		/**
		 * The amount of time in seconds until the data expires.
		 *
		 * @var integer|null
		 */
		protected $lifetime;

		/**
		 * A timestamp that represents when this data expires.
		 *
		 * @var integer
		 */
		protected $expires;

		/**
		 * CacheData constructor.
		 *
		 * @param string  $key
		 * @param array   $data
		 * @param integer $lifetime
		 */
		public function __construct($key, $data = array(), $lifetime = 86400) {
			$this->setKey($key);
			$this->setData($data);
			$this->setLifetime($lifetime);

			$this->updateExpirationTime();
		}

		/**
		 * Sets the identifier for the cached data.
		 *
		 * @param string $key The unique identifier for the cached data.
		 */
		public function setKey($key) {
			if (!is_string($key) && !is_int($key)) {
				$key = null;
			}

			$this->key = $key;
		}

		/**
		 * Returns the unique identifier for this data.
		 *
		 * @return string
		 */
		public function getKey() {
			return $this->key;
		}

		/**
		 * Sets the data that is being cached.
		 *
		 * @param $data
		 */
		public function setData($data) {
			$this->data = $data;
		}

		/**
		 * Returns the cached data.
		 *
		 * @return array|mixed
		 */
		public function getData() {
			return $this->data;
		}

		/**
		 * Set the lifetime of this data.
		 *
		 * @param integer $lifetime The number of seconds before this data expires.
		 */
		public function setLifetime($lifetime) {
			$this->lifetime = intval($lifetime);

			if ($this->lifetime < 0) {
				$this->lifetime = 0;
			}
		}

		/**
		 * Returns the lifetime of this data.
		 *
		 * @return integer
		 */
		public function getLifetime() {
			return $this->lifetime;
		}

		/**
		 * Change the expiration time so that it has the full lifetime before it expires.
		 *
		 * @return bool
		 */
		public function updateExpirationTime() {
			if ($this->lifetime < 1) {
				$this->expires = 0;
				return true;
			}

			$this->expires = time() + $this->lifetime;

			return true;
		}

		/**
		 * Tells whether the cached data has expired or not.
		 *
		 * @return bool
		 */
		public function isExpired() {
			if ($this->expires === 0) {
				return false;
			}

			return time() > $this->expires;
		}
	}
