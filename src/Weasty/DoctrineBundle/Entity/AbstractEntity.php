<?php
namespace Weasty\DoctrineBundle\Entity;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AbstractEntity
 * @package Weasty\DoctrineBundle\Mapper
 */
abstract class AbstractEntity implements \ArrayAccess {

    /**
     * @var array
     */
    private $temps = array();

    /**
     * @var array
     */
    protected $files_names = array();

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @return array]
     */
    protected function getFilesNames(){
        return $this->files_names;
    }

    /**
     * @return array
     */
    protected function getFiles(){
        return $this->files;
    }

    /**
     * Sets file.
     * @param $file_name_field
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile($file_name_field, UploadedFile $file = null)
    {
        $file_name = $this->offsetGet($file_name_field);
        $this->files[$file_name_field] = $file;
        $this->offsetSet($file_name_field, $file ? $file->getClientOriginalName() : null);
        // check if we have an old image path
        if ($file_name) {
            // store the old name to delete after the update
            $this->temps[$file_name] = $file_name;
            $this->offsetSet($file_name_field, null);
        }
        return $this;
    }

    /**
     * @param $file_name_field
     * @return UploadedFile|null
     */
    public function getFile($file_name_field)
    {
        return isset($this->files[$file_name_field]) ? $this->files[$file_name_field] : null;
    }

    public function preUpload()
    {

        $files = $this->getFiles();

        if($files){

            foreach($files as $file_name_field => $file){

                if($file instanceof UploadedFile){

                    $filename = sha1(uniqid(mt_rand(), true));
                    $this->offsetSet($file_name_field, $filename.'.'.$file->getClientOriginalExtension());

                }

            }

        }

        return $this;

    }

    public function upload()
    {

        $files = $this->getFiles();

        if($files){

            foreach($files as $file_name_field => $file){

                if($file instanceof UploadedFile){

                    $file->move($this->getUploadDirPath(), $this->offsetGet($file_name_field));

                }

            }

        }

        foreach($this->temps as $temp_file_name){

            $temp_file_path = $this->getUploadDirPath().'/'.$temp_file_name;
            if(is_file($temp_file_path)){
                unlink($temp_file_path);
            }

        }

        $this->files = array();
        $this->temps = array();

        return $this;

    }

    public function removeUpload()
    {
        foreach($this->getFilesNames() as $file_name){
            $file_path = $this->getUploadDirPath() . '/' . $file_name;
            if(is_file($file_path)){
                @unlink($file_path);
            }
        }
        return $this;
    }

    /**
     * @param $file_name
     * @return null|string
     */
    public function getFilePath($file_name)
    {
        return !$file_name
            ? null
            : $this->getUploadDirPath() . '/' . $file_name;
    }

    /**
     * @param $file_name
     * @return null|string
     */
    public function getFileUrl($file_name)
    {
        return !$file_name
            ? null
            : '/' . $this->getUploadDirName() . '/' . $file_name;
    }

    /**
     * @return string
     */
    public function getUploadDirPath()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return realpath(__DIR__.'/../../../../web/'.$this->getUploadDirName());
    }

    /**
     * @return string
     */
    protected function getUploadDirName()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        return method_exists($this, $method);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            $this->$method($value);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $method = 'set' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            $this->$method(null);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {

        if(strpos($name, '(') !== false){

            list($property, $argumentsList) = explode('(', $name);

            $method = 'get' . Inflector::classify($property);
            $arguments = explode(',', str_replace(')', '', $argumentsList));

            if(method_exists($this, $method)){
                return call_user_func_array(array($this, $method), $arguments);
            } else {
                return null;
            }

        } else {
            return $this->offsetGet($name);
        }
    }

}
