<?php
/**
 * THIS IS AN AUTOGENERATED FILE
 * DO NOT MODIFY
 */


namespace Ls\Omni\Client\Loyalty\Entity;

use Ls\Omni\Client\IRequest;

class BasketCalc implements IRequest
{

    /**
     * @property BasketCalcRequest $basketRequest
     */
    protected $basketRequest = null;

    /**
     * @param BasketCalcRequest $basketRequest
     * @return $this
     */
    public function setBasketRequest($basketRequest)
    {
        $this->basketRequest = $basketRequest;
        return $this;
    }

    /**
     * @return BasketCalcRequest
     */
    public function getBasketRequest()
    {
        return $this->basketRequest;
    }


}
