<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

trait EncryptableTrait
{
    /*
        Todo: things that need to be done
        1) Try to build this functionality for bulk inserts functions - edit: cannot do that
        2) Try to make two encryption types like ['unique', 'fixed'],
            Unique: will generate unique encryption everytime for same string
            Fixed: will generate same encryption everytime for same string
        3) Try to create a command "Command to roll key" that if APP_KEY get changed in .env
            it go through all models encryption, decrypt them and encrypt them using new key.
        4) Try to add conditions like if status = ABC then dont encrypt for that row else encrypt.

        contributers:
        https://github.com/betterapp/laravel-db-encrypter
        https://laravel-news.com/laravel-db-encrypter.
        https://stackoverflow.com/a/58857962
        https://stackoverflow.com/a/52024402
    */

    private static $ENCRYPT = 'encrypt';

    // protected $encryptable = [];

    /**
     * If the attribute is in the encryptable or casts array
     * then decrypt it.
     *
     * @param  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!$key) return;

        $value = $this->attributes[$key] ?? null;

        // if the attribute doesn't exists in $encryptable array or in casts array
        // then we well let the default functionality handle it.
        if (!$this->isValidDecryptKeyValue($key, $value) ) {
            return parent::getAttribute($key);
        }

        // if attribute is decryptabel then we decrypt it instantly so we can apply model
        // casts and mutators.
        $this->attributes[$key] = self::decrypt($value);

        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
        if (array_key_exists($key, $this->attributes) ||
            array_key_exists($key, $this->casts) ||
            $this->hasGetMutator($key) ||
            $this->isClassCastable($key)) {
            return $this->getAttributeValue($key);
        }

        // Here we will determine if the model base class itself contains this given key
        // since we don't want to treat any of those methods as relationships because
        // they are all intended as helper methods and none of these are relations.
        if (method_exists(self::class, $key)) {
            return;
        }

        return $this->getRelationValue($key);
    }

    /**
     * If the attribute is in the encryptable or casts array
     * then encrypt it.
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        // if the attribute doesn't exists in $encryptable array or in casts array
        // then we well let the default functionality handle it.
        if (!$this->isValidEncryptKeyValue($key, $value)) {
            return parent::setAttribute($key, $value);
        }

        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // this model, such as "json_encoding" a listing of data for storage.
        if ($this->hasSetMutator($key)) {
            $this->setMutatedAttributeValue($key, $value);
            $this->attributes[$key] = self::encrypt($this->attributes[$key]);
            return;
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        elseif ($value && $this->isDateAttribute($key)) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isClassCastable($key)) {
            $this->setClassCastableAttribute($key, $value);
            $this->attributes[$key] = self::encrypt($this->attributes[$key]);
            return $this;
        }

        if (! is_null($value) && $this->isJsonCastable($key)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        // If this attribute contains a JSON ->, we'll set the proper value in the
        // attribute's underlying array. This takes care of properly nesting an
        // attribute in the array's value in the case of deeply nested items.
        if (Str::contains($key, '->')) {
            $this->fillJsonAttribute($key, $value);
            $this->attributes[$key] = self::encrypt($this->attributes[$key]);
            return $this;
        }

        if (! is_null($value) && $this->isEncryptedCastable($key)) {
            $value = $this->castAttributeAsEncryptedString($key, $value);
        } else {
            $value = self::encrypt($value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * When need to make sure that we iterate through
     * all the keys.
     *
     * @return array
     */
    public function attributesToArray()
    {
        if (!isset($this->attributes['level'])) return parent::attributesToArray();
        if ($this->attributes['level'] == 1001) return parent::attributesToArray();

        // decrypt attributes before casts
        $this->attributes = $this->decryptAttributes($this->attributes);

        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    /**
     * Get all of the current attributes on the model for an insert operation.
     *
     * @return array
     */
    protected function getAttributesForInsert()
    {
        // todo: use this for conditional encription if condition match then encypt or else dont encrypt.
        // dd('insert', $this->getAttributes());
        return $this->getAttributes();
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function decryptAttributes(array $attributes): array
    {
        $decryptedKeys = [];

        foreach ($this->encryptable as $key) {
            if (isset($attributes[$key]) && $attributes[$key] !== '' && !is_null($attributes[$key])) {
                $attributes[$key] = self::decrypt($attributes[$key]);
                $decryptedKeys[] = $key;
            }
        }
        foreach ($this->casts as $key => $value) {
            if (in_array($key, $decryptedKeys)) continue;
            if($value == self::$ENCRYPT) {
                if (isset($attributes[$key]) && $attributes[$key] !== '' && !is_null($attributes[$key])) {
                    $attributes[$key] = self::decrypt($attributes[$key]);
                }
            }
        }

        return $attributes;
    }

    private function isValidEncryptKeyValue($key, $value)
    {
        return (isset($this->casts[$key]) && $value !== '' && !is_null($value) && $this->casts[$key] == self::$ENCRYPT)
            ||
            (in_array($key, $this->encryptable) && $value !== '' && !is_null($value));
    }

    private function isValidDecryptKeyValue($key, $value)
    {
        return (isset($this->casts[$key]) && $value !== '' && !is_null($value) && $this->casts[$key] == self::$ENCRYPT)
            ||
            (in_array($key, $this->encryptable) && $value !== '' && !is_null($value));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function encrypt($value)
    {
        try {
            $value = Crypt::encrypt($value);
        } catch (EncryptException $e) {}

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private static function decrypt($value)
    {
        try {
            $value = Crypt::decrypt($value);
        } catch (DecryptException $e) {}

        return $value;
    }
}



