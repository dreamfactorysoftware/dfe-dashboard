<?php namespace DreamFactory\Library\Console\Components;

use DreamFactory\Library\Utility\JsonFile;

/**
 * A generic KVP collection
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array
     */
    protected $_contents;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array $contents Initial contents of the collection
     */
    public function __construct(array $contents = [])
    {
        $this->_contents = $contents;
    }

    /**
     * @return Collection
     */
    public function clear()
    {
        $this->_contents = [];

        return $this;
    }

    /**
     * @param string $format
     *
     * @return array
     */
    public function all($format = null)
    {
        switch ($format) {
            case 'json':
                return JsonFile::encode($this->_contents);
        }

        return $this->_contents;
    }

    /**
     * @param string $key Item to retrieve.
     * @param mixed  $defaultValue
     * @param bool   $burnAfterReading
     *
     * @return mixed Value of the key or default value
     */
    public function get($key = null, $defaultValue = null, $burnAfterReading = false)
    {
        if (null === $key) {
            return $this->all();
        }

        if (array_key_exists($key, $this->_contents)) {
            $_value = $this->_contents[$key];

            if ($burnAfterReading) {
                unset($this->_contents[$key]);
            }
        } else {
            $this->_contents['$key'] = $_value = $defaultValue;
        }

        return $_value;
    }

    /**
     * Set a key value pair
     *
     * @param string $key   Key to set
     * @param mixed  $value Value to set
     * @param bool   $overwrite
     *
     * @return Collection Returns a reference to the object
     */
    public function set($key, $value, $overwrite = true)
    {
        if (!$overwrite && array_key_exists($key, $this->_contents)) {
            throw new \LogicException('Key "' . $key . '" is read-only.');
        }

        $this->_contents[$key] = $value;

        return $this;
    }

    /**
     * Add a value to the collection.
     * If the key exists, the value is converted to an array
     * and the new value is pushed on the end of the array.
     *
     * @param string $key   Key to add
     * @param mixed  $value Value to add
     *
     * @return Collection
     */
    public function add($key, $value)
    {
        if (!array_key_exists($key, $this->_contents)) {
            $this->_contents[$key] = $value;

            return $this;
        }

        if (is_array($this->_contents[$key])) {
            $this->_contents[$key] = array_merge($this->_contents[$key], $value);

            return $this;
        }

        $this->_contents[$key] = [$this->_contents[$key], $value];

        return $this;
    }

    /**
     * @param string $key key to remove
     *
     * @return Collection
     */
    public function remove($key)
    {
        if (isset($this->_contents[$key])) {
            unset($this->_contents[$key]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_contents);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_contents);
    }

    /**
     * @param string $value Value to find
     *
     * @return mixed Returns the key of found value or FALSE
     */
    public function hasValue($value)
    {
        return array_search($value, $this->_contents, true);
    }

    /**
     * @param array $data Replacement array of data
     *
     * @return Collection
     */
    public function replace(array $data)
    {
        $this->_contents = $data;

        return $this;
    }

    /**
     * @param Collection|array $data array of key value pair data or a collection
     *
     * @return Collection Returns a reference to the object.
     */
    public function merge($data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_contents);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_contents);
    }
}