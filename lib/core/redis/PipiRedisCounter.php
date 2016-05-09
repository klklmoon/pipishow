<?php
/**
 * Represents a redis counter that can be atomically incremented and decremented.
 * <pre>
 * $counter = new ARedisCounter("totalPageViews");
 * $counter->increment();
 * echo $counter->getValue();
 * </pre>
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * 
 * @author Charles Pick
 * @version $Id: PipiRedisCounter.php 8375 2013-04-02 09:06:28Z leiwei $ 
 */
class PipiRedisCounter extends PipiRedisEntity {

	/**
	 * The value of the counter
	 * @var integer
	 */
	protected $_value;

	/**
	 * Removes all the items from the entity
	 * @return PipiRedisIterableEntity the current entity
	 */
	public function clear() {
		$this->_value = null;
		$this->getConnection()->getClient()->delete($this->name);
		return $this;
	}

	/**
	 * Gets the value of the counter
	 * @param boolean $forceRefresh whether to fetch the data from redis again or not
	 * @return integer the value of the counter
	 */
	public function getValue($forceRefresh = false) {
		if ($this->_value === null || $forceRefresh) {
			if ($this->name === null) {
				throw new CException(get_class($this)." requires a name!");
			}
			$this->_value = (int) $this->getConnection()->getClient()->get($this->name);
		}
		return $this->_value;
	}
	/**
	 * Increments the counter by the given amount
	 * @param integer $byAmount the amount to increment by, defaults to 1
	 * @return integer the new value of the counter
	 */
	public function increment($byAmount = 1) {
		if ($this->name === null) {
			throw new CException(get_class($this)." requires a name!");
		}
		return $this->_value = (int) $this->getConnection()->getClient()->incrBy($this->name,$byAmount);
	}
	
	public function incr(){
		if ($this->name === null) {
			throw new CException(get_class($this)." requires a name!");
		}
		return $this->_value = (int) $this->getConnection()->getClient()->incr($this->name);
	}

	/**
	 * Decrements the counter by the given amount
	 * @param integer $byAmount the amount to decrement by, defaults to 1
	 * @return integer the new value of the counter
	 */
	public function decrement($byAmount = 1) {
		if ($this->name === null) {
			throw new CException(get_class($this)." requires a name!");
		}
		return $this->_value = (int) $this->getConnection()->getClient()->decrBy($this->name,$byAmount);
	}

	/**
	 * Gets the value of the counter
	 * @return integer the value of the counter
	 */
	public function __toString() {
		return $this->getValue();
	}
}