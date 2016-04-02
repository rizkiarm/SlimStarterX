<?php

namespace SlimStarterX\Base;

class Model extends \Illuminate\Database\Eloquent\Model {

    protected $errors;

    protected static function rules()
    {
        return [];
    }

    protected static function messages()
    {
        return [];
    }

    public static function findOne($attribute, $operator, $value){
        $result = static::findOneInternal($attribute, $operator, $value);
        return $result->count() > 0 ? $result : null;
    }

    public static function scopeFindOneInternal($query, $attribute, $operator, $value)
    {
        return $query->where($attribute, $operator, $value)->first();
    }

    public static function scopeFindAll($query, $attribute, $operator, $value)
    {
        return $query->where($attribute, $operator, $value)->get();
    }

    public function save(array $options = array())
    {
        return $this->internalSave($options, true);
    }

    public function forceSave(array $options = array())
    {
        return $this->internalSave($options, false);
    }

    protected function internalSave(array $options = array(), $runValidation = true)
    {
        if($runValidation && !$this->validate()) return false;

        if($this->beforeSave() && parent::save($options)){
            $this->afterSave();
            return true;
        }

        return false;
    }

    public function beforeSave()
    {
        return true;
    }

    public function afterSave()
    {
    }

    public function delete()
    {
        if($this->beforeDelete() && parent::delete()){
            $this->afterDelete();
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        return true;
    }

    public function afterDelete()
    {
    }

    public function validate()
    {
        $v = \Validator::make($this->attributes, static::rules(), static::messages());
        if (!$v->passes())
        {
            $this->setErrors($v->messages());
            return false;
        }
        return true;
    }

    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return ! empty($this->errors);
    }

    public function load($data)
    {
        return $this->fill($data);
    }

    public function getColumnListing()
    {
        return \DB::connection()->getSchemaBuilder()->getColumnListing($this->table);
    }

    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws Exception if the property is not defined or if the property is write-only
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif ($this->canGetProperty($name)) {
            return $this->getAttribute($name);
        } elseif (method_exists($this, 'set' . $name)) {
            throw new Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws Exception if the property is not defined or if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif ($this->canSetProperty($name)) {
            $this->setAttribute($name, $value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new Exception('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     * @param string $name the property name or the event name
     * @return boolean whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return ((isset($this->attributes[$name]) || isset($this->relations[$name])) ||
                ($this->hasGetMutator($name) && ! is_null($this->getAttributeValue($name))));
        }
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     * @param string $name the property name
     * @throws Exception if the property is read only.
     * @see http://php.net/manual/en/function.unset.php
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new Exception('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            unset($this->attributes[$name], $this->relations[$name]);
        }
    }

    /**
     * Returns a value indicating whether a property is defined.
     * A property is defined if:
     *
     * - the class has a getter or setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * A property is readable if:
     *
     * - the class has a getter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'get' . $name)  || array_key_exists($name, $this->attributes) || in_array($name, $this->columnListing) || $checkVars && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * A property is writable if:
     *
     * - the class has a setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'set' . $name) || array_key_exists($name, $this->attributes) || in_array($name, $this->columnListing) || $checkVars && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     * @param string $name the method name
     * @return boolean whether the method is defined
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
}