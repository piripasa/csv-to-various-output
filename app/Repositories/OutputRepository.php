<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 3:10 AM
 */

namespace App\Repositories;


class OutputRepository
{
    private $fileName;
    private $fileVersioning;
    private $data;
    private $options;

    /**
     * Set output file name
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        if (!is_string($fileName) || empty($fileName)) {
            throw new \InvalidArgumentException(
                "The filename is invalid.");
        }

        if ($this->fileVersioning) {
            $fileName .= '-' . date('Y-m-d H:i:s');
        }

        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Set output file version
     * @param $fileVersioning
     * @return $this
     */
    public function setFileVersioning($fileVersioning)
    {
        $this->fileVersioning = $fileVersioning;
        return $this;
    }

    /**
     * Set processed data based on filter/sorting/group
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        if (!is_array($data) || empty($data)) {
            throw new \InvalidArgumentException(
                "The data is invalid.");
        }

        if (count($this->options) > 0) {
            if (array_key_exists('filter', $this->options)) {
                $data = arrayFilter($data, $this->options['filter'], $this->options['filter_value']);
            }

            if (array_key_exists('sort', $this->options)) {
                $data = arraySort($data, $this->options['sort'], $this->options['sort_order']);
            }

            if (array_key_exists('group', $this->options)) {
                $data = arrayGroup($data, $this->options['group']);
            }

        }

        $this->data = $data;
        return $this;
    }

    /**
     * Set output file Options
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Save data to file
     * @param OutputInterface $file
     */
    public function save(OutputInterface $file)
    {
        $file->saveData($this->fileName, $this->data);
    }
}