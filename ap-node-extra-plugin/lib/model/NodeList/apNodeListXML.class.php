<?php 

/**
 * apNodeListXML
 * 
 * xml node list
 * Code in part taken from the wifidog-auth server's /wifidog/classes/NodeLists/NodeListXML.php
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

class apNodeListXML extends NodeListAbstract  {
  /**
   * XML DOM Document that will contain all the data concerning the nodes
   *
   * @var object
   */
  private $_xmldoc;

  /**
   * Constructor
   *
   * @param array  Parameters for this node list
   */
  public function __construct($parameters = array())
  {
    // Init XML Document
    $this->_xmldoc = new DOMDocument("1.0", "UTF-8");
    $this->_xmldoc->formatOutput = true;
    
    parent::__construct();

  }

    /**
     * Sets header of output
     *
     * @return void
     */
    public function setHeader($response)
    {
        $response->setContentType('text/xml');
        $response->addVaryHttpHeader('Accept-Language');
        $response->addCacheControlHttpHeader('no-cache');
        
       // header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); # Past date
        //header("Pragma: no-cache");
        //header("Content-Type: text/xml; charset=UTF-8");
    }

    /**
     * Retreives the output of this object.
     *
     * @return string The XML output
     */
    public function getOutput()
    {
        $arr = $this->generateList();  
 
        return $this->getXmlLegacy();
        
    }
    
    /**
     * Generates the xml document like the old wifidog used to do, so that application using the old wifidog url and schema
     *   don't fail
     */
    protected function getXmlLegacy($return_object = false) {
           // Root node
        $_hotspotStatusRootNode = $this->_xmldoc->createElement("wifidogHotspotsStatus");
        $_hotspotStatusRootNode->setAttribute('version', '1.0');
        $this->_xmldoc->appendChild($_hotspotStatusRootNode);

        // Document metadata
        $_documentGendateNode = $this->_xmldoc->createElement("generationDateTime", gmdate("Y-m-d\Th:m:s\Z"));
        $_hotspotStatusRootNode->appendChild($_documentGendateNode);

        // Node details
        
        // Hotspots metadata
        $_hotspotsMetadataNode = $this->_xmldoc->createElement("hotspots");
        $_hotspotsMetadataNode = $_hotspotStatusRootNode->appendChild($_hotspotsMetadataNode);

        foreach ($this->nodes as $_node) {

            $_hotspot = $this->_xmldoc->createElement("hotspot");
            $_hotspot = $_hotspotsMetadataNode->appendChild($_hotspot);

            // Hotspot ID
            $_hotspotId = $this->_xmldoc->createElement("hotspotId", $_node->getId());
            $_hotspot->appendChild($_hotspotId);

            // Hotspot name
            $_hotspotName = $this->_xmldoc->createElement("name", htmlspecialchars($_node->getName(), ENT_QUOTES));
            $_hotspot->appendChild($_hotspotName);

            /**
             * (1..n) A Hotspot has many node
             *
             * WARNING For now, we are simply duplicating the hotspot data in node
             * Until wifidog implements full abstractiong hotspot vs nodes.
             */
            $_nodes = $this->_xmldoc->createElement("nodes");
            $_hotspot->appendChild($_nodes);

            $_nodeMetadataNode = $this->_xmldoc->createElement("node");
            $_nodes->appendChild($_nodeMetadataNode);

            // Node ID
            $_nodeId = $this->_xmldoc->createElement("nodeId", $_node->getId());
            $_nodeMetadataNode->appendChild($_nodeId);

            // TODO: add this Online Users
           // $_nodeUserNum = $this->_xmldoc->createElement("numOnlineUsers", $_node->getNumActiveConnections());
            //$_nodeMetadataNode->appendChild($_nodeUserNum);

            $_nodeCreationDate = $this->_xmldoc->createElement("creationDate", $_node->getCreatedAt());
            $_nodeMetadataNode->appendChild($_nodeCreationDate);

            if ($_node->getDeploymentStatus() != 'NON_WIFIDOG_NODE') {
                if ($_node->isOnline()) {
                    $_nodeStatus = $this->_xmldoc->createElement("status", "up");
                } else {
                    $_nodeStatus = $this->_xmldoc->createElement("status", "down");
                }

                $_nodeMetadataNode->appendChild($_nodeStatus);
            }

           
            $_nodeGis = $this->_xmldoc->createElement("gisLatLong");
            $_nodeGis->setAttribute("lat", $_node->getLatitude());
            $_nodeGis->setAttribute("long", $_node->getLongitude());
            $_nodeMetadataNode->appendChild($_nodeGis);
        

            // Hotspot opening date ( for now it's called creation_date )
            $_hotspotOpeningDate = $this->_xmldoc->createElement("openingDate", $_node->getCreatedAt());
            $_hotspot->appendChild($_hotspotOpeningDate);

            // Hotspot Website URL
            //if ($_node->getWebSiteURL() != "") {
            //    $_hotspotUrl = $this->_xmldoc->createElement("webSiteUrl", htmlspecialchars('', ENT_QUOTES));
            //    $_hotspot->appendChild($_hotspotUrl);
            //}

            // Hotspot global status
            if ($_node->getDeploymentStatus() != 'NON_WIFIDOG_NODE') {
                if ($_node->isOnline()) {
                    $_hotspotStatus = $this->_xmldoc->createElement("globalStatus", "100");
                } else {
                    $_hotspotStatus = $this->_xmldoc->createElement("globalStatus", "0");
                }

                $_hotspot->appendChild($_hotspotStatus);
            }

            // Description
            if ($_node->getDescription() != "") {
                $_hotspotDesc = $this->_xmldoc->createElement("description", htmlspecialchars($_node->getDescription(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotDesc);
            }

            // Mass transit info
            if ($_node->getMassTransitInfo() != "") {
                $_hotspotTransit = $this->_xmldoc->createElement("massTransitInfo", htmlspecialchars($_node->getMassTransitInfo(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotTransit);
            }

            // Contact e-mail
            if ($_node->getPublicEmail() != "") {
                $_hotspotContactEmail = $this->_xmldoc->createElement("contactEmail", $_node->getPublicEmail());
                $_hotspot->appendChild($_hotspotContactEmail);
            }

            // Contact phone
            if ($_node->getPublicPhoneNumber() != "") {
                $_hotspotContactPhone = $this->_xmldoc->createElement("contactPhoneNumber", $_node->getPublicPhoneNumber());
                $_hotspot->appendChild($_hotspotContactPhone);
            }

            // Civic number
            if ($_node->getCivicNumber() != "") {
                $_hotspotCivicNr = $this->_xmldoc->createElement("civicNumber", $_node->getCivicNumber());
                $_hotspot->appendChild($_hotspotCivicNr);
            }

            // Street address
            if ($_node->getStreetName() != "") {
                $_hotspotStreet = $this->_xmldoc->createElement("streetAddress", htmlspecialchars($_node->getStreetName(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotStreet);
            }

            // City
            if ($_node->getCity() != "") {
                $_hotspotCity = $this->_xmldoc->createElement("city", htmlspecialchars($_node->getCity(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotCity);
            }

            // Province
            if ($_node->getProvince() != "") {
                $_hotspotProvince = $this->_xmldoc->createElement("province", htmlspecialchars($_node->getProvince(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotProvince);
            }

            // Postal code
            if ($_node->getPostalCode() != "") {
                $_hotspotPostalCode = $this->_xmldoc->createElement("postalCode", $_node->getPostalCode());
                $_hotspot->appendChild($_hotspotPostalCode);
            }

            // Country
            if ($_node->getCountry() != "") {
                $_hotspotCountry = $this->_xmldoc->createElement("country", htmlspecialchars($_node->getCountry(), ENT_QUOTES));
                $_hotspot->appendChild($_hotspotCountry);
            }

            // Long / Lat
            $_hotspotGis = $this->_xmldoc->createElement("gisCenterLatLong");
            $_hotspotGis->setAttribute("lat", $_node->getLatitude());
            $_hotspotGis->setAttribute("long", $_node->getLongitude());
            //$_hotspotGis->setAttribute("show", $_node->showOnMap());
            $_hotspot->appendChild($_hotspotGis);
            

        }
        if ($return_object) {
            return $this->_xmldoc;
        } else {
            return $this->_xmldoc->saveXML();
        }
    }
  
}
  
