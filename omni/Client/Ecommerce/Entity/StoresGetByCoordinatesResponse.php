<?php
/**
 * THIS IS AN AUTOGENERATED FILE
 * DO NOT MODIFY
 */


namespace Ls\Omni\Client\Ecommerce\Entity;

use Ls\Omni\Client\IResponse;

class StoresGetByCoordinatesResponse implements IResponse
{

    /**
     * @property ArrayOfStore $StoresGetByCoordinatesResult
     */
    protected $StoresGetByCoordinatesResult = null;

    /**
     * @param ArrayOfStore $StoresGetByCoordinatesResult
     * @return $this
     */
    public function setStoresGetByCoordinatesResult($StoresGetByCoordinatesResult)
    {
        $this->StoresGetByCoordinatesResult = $StoresGetByCoordinatesResult;
        return $this;
    }

    /**
     * @return ArrayOfStore
     */
    public function getStoresGetByCoordinatesResult()
    {
        return $this->StoresGetByCoordinatesResult;
    }

    /**
     * @return ArrayOfStore
     */
    public function getResult()
    {
        return $this->StoresGetByCoordinatesResult;
    }


}
