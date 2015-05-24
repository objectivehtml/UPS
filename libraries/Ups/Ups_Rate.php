<?php
/**
 * Date: 22/05/15
 *
 * @author Tamara Fedorova
 */


class Ups_Rate
{
    protected $_code;

    protected $_serviceName;

    protected $_guaranteedDelivery;

    protected $_price;

    protected static $service_codes = array(
        '01'    => 'UPS Express',
        '02'    => 'UPS Expedited',
        '03'    => 'UPS Ground',
        '07'    => 'UPS Express',
        '08'    => 'UPS Expedited',
        '11'    => 'UPS Standard',
        '12'    => 'UPS Three-Day Select',
        '13'    => 'UPS Saver',
        '14'    => 'UPS Express Early A.M.',
        '54'    => 'UPS Worldwide Express Plus',
        '59'    => 'UPS Second Day Air A.M.',
        '65'    => 'UPS Saver',
        '82'    => 'UPS Today Standard',
        '83'    => 'UPS Today Dedicated Courrier',
        '84'    => 'UPS Today Intercity',
        '85'    => 'UPS Today Express',
        '86'    => 'UPS Today Express Saver',
        '308'   => 'UPS Freight LTL',
        '309'   => 'UPS Freight LTL Guaranteed',
        '310'   => 'UPS Freight LTL Urgent',
        'TDCB'  => 'Trade Direct Cross Border',
        'TDA'   => 'Trade Direct Air',
        'TDO'   => 'Trade Direct Ocean',
    );

    /**
     * @param SimpleXMLElement $xmlRateDescription
     */

    public function __construct($xmlRateDescription)
    {


        $this->_code = $xmlRateDescription->Service->Code->__toString();

        $this->_price = number_format((double)($xmlRateDescription->TotalCharges->MonetaryValue), 2);

        $this->_serviceName = self::$service_codes[$this->_code];

        if (!empty($xmlRateDescription->GuaranteedDaysToDelivery))
        {
            $this->_guaranteedDelivery = $xmlRateDescription->GuaranteedDaysToDelivery->__toString();
        }

    }
}