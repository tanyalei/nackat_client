<?php
namespace NationalCatalog;

/**
 * Class Api
 *
 * @property string HeaderResponseCode
 * @property string HeaderETag
 * @property string HeaderAPIUsageLimit
 * @property string HeaderRetryAfter
 *
 * @package NationalCatalog
 */
final class ApiClient
{
    const API_URL = 'http://api.crpt.my';
    const VERSION = 'v3';

    const RESPONSE_FORMAT_JSON = 'json';
    const RESPONSE_FORMAT_XML = 'xml';

    const REQUEST_ENTITY_ATTRIBUTES = 'attributes';
    const REQUEST_ENTITY_BRANDS = 'brands';
    const REQUEST_ENTITY_CATEGORIES = 'categories';
    const REQUEST_ENTITY_LOCATIONS = 'locations';
    const REQUEST_ENTITY_PARTIES = 'parties';
    const REQUEST_ENTITY_PRODUCTS = 'product';
    const REQUEST_ENTITY_ETAGS_LIST = 'etagslist';
    const REQUEST_ENTITY_SUGGESTIONS = 'suggestions';
    const REQUEST_ENTITY_ADD_REVIEW = 'addreview';
    const REQUEST_ENTITY_FEED = 'feed';
    const REQUEST_ENTITY_FEED_STATUS = 'feed-status';

    const CODE_STATUS_OK = 200;
    const CODE_STATUS_NOT_MODIFIED = 304;
    const CODE_STATUS_REQUEST_ERROR = 400;
    const CODE_STATUS_NOT_AUTHORIZED = 401;
    const CODE_STATUS_NO_ACCESS = 403;
    const CODE_STATUS_NO_DATA_FOUND = 404;
    const CODE_STATUS_REQUEST_ENTITY_TO_LARGE = 413;
    const CODE_STATUS_REQUEST_LIMIT_REACHED = 429;
    const CODE_STATUS_INTERNAL_SERVER_ERROR = 500;
    const CODE_STATUS_METHOD_NOT_FOUND = 501;
    const CODE_STATUS_SERVICE_NOT_AVAILABLE = 503;

    const ATTRIBUTE_TYPE_ALL = 'a';
    const ATTRIBUTE_TYPE_MANDATORY = 'm';
    const ATTRIBUTE_TYPE_RECOMMEND = 'r';
    const ATTRIBUTE_TYPE_OPTIONAL = 'o';

    const SOCIAL_TYPE_GOOGLE_PLUS = 'gp';
    const SOCIAL_TYPE_FACEBOOK = 'fb';
    const SOCIAL_TYPE_TWITTER = 'tw';
    const SOCIAL_TYPE_VK = 'vk';

    protected $apiKey;
    protected $supplierKey;
    protected $apiUrl;
    protected $format;
    /** @var string */
    private $_error;
    /** @var array */
    private $_headers;

    /**
     * ListexApi constructor.
     * @param $apiKey
     */
    public function __construct($apiKey, $supplierKey = null)
    {
        $this->apiUrl = self::API_URL . '/' . self::VERSION;
        $this->apiKey = $apiKey;
        $this->supplierKey = $supplierKey;
        $this->format = RESPONSE_FORMAT_JSON;
        $this->_error = null;
        $this->_headers = null;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (0 === strpos($property, 'Header') && array_key_exists($header = str_replace('Header', '', $property),
                $this->_headers)) {
            return $this->_headers[$header];
        }
        return null;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->API_URL = $url;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        if (in_array($format, [RESPONSE_FORMAT_JSON, RESPONSE_FORMAT_XML])) {
            $this->format = $format;
        } else {
            throw new Exception("Format is not supported");
        }
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return null !== $this->_error;
    }

    /**
     * Return last error
     * @return null|string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Return last HTTP Code
     * @return null|string
     */
    public function getHttpCode()
    {
        return $this->HeaderResponseCode;
    }

    /**
     * Return last ETag
     * @return null|string
     */
    public function getLastETag()
    {
        return $this->HeaderETag;
    }

    /**
     * Return current usage count requests
     * @return null|string
     */
    public function getCurrentUsageCount()
    {
        if (null !== $this->HeaderAPIUsageLimit && false !== strpos($this->HeaderAPIUsageLimit, '/')) {
            return explode('/', $this->HeaderAPIUsageLimit)[0];
        }
        return null;
    }

    /**
     * Return requests limit
     * @return null|string
     */
    public function getUsageLimit()
    {
        if (null !== $this->HeaderAPIUsageLimit && false !== strpos($this->HeaderAPIUsageLimit, '/')) {
            return explode('/', $this->HeaderAPIUsageLimit)[1];
        }
        return $this->HeaderAPIUsageLimit;
    }

    /**
     * @return null|string
     */
    public function getRetryAfter()
    {
        return $this->HeaderRetryAfter;
    }

    /**
     * Send request 
     *
     * @param string $requestEntity
     * @param array $params
     * @param array $headers
     * @return bool|string Return the result on success, FALSE on failure
     */
    private function sendRequest($url,  array $params = [], array $headers = []) {
        $this->_error = null;
        $this->_headers = null;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (count($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($curl);
        if (false === $response) {
            $this->_error = 'Error (' . curl_errno($curl) . '): ' . curl_error($curl);
        } else {
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            if (strlen($response) === $header_size) {
                $body = '';
            } else {
                $body = substr($response, $header_size);
            }
            $response = $body;
            $this->_headers = array_reduce(explode("\r\n", $header), function ($result, $header) {
                if (false === strpos($header, ':')) {
                    return $result;
                }
                $key = explode(':', $header)[0];
                $value = trim(str_replace($key . ':', '', $header), " \t\"'");
                $result[str_replace('-', '', $key)] = $value;
                return $result;
            }, []);
        }
        $this->_headers['ResponseCode'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $response;
    }

    /**
     * Send request and return pure response
     *
     * @param string $requestEntity
     * @param array $params
     * @param string $ETag ETag
     * @return bool|string Return the result on success, FALSE on failure
     */
    public function getPureResponse($requestEntity, array $params = [], $ETag = null) {
        $params['format'] = $this->format;
        $params['apikey'] = $this->apiKey;
        if ($this->supplierKey) {
            $params['supplier_key'] = $this->supplierKey;
        }
        $headers = [];
        if (null !== $ETag) {
            $headers[] = 'If-None-Match: "' . $ETag . '"'
        }
        return $this->sendRequest($this->getUrl($requestEntity), $params, $headers);
    }

    /**
     * Get response
     *
     * @param string $requestEntity
     * @param array $params
     * @param string $ETag ETag
     * @return bool|array
     */
    public function parseResponse($result)
    {
        $response = false !== $result ? json_decode($result, true) : false;
        print_r($response);
        if (false !== $response) {
            switch ($this->getHttpCode()) {
                case self::CODE_STATUS_OK:
                    break;
                case self::CODE_STATUS_REQUEST_ERROR:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): request error';
                    break;
                case self::CODE_STATUS_NOT_MODIFIED:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): not modified';
                    break;
                case self::CODE_STATUS_NOT_AUTHORIZED:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): not authorized';
                    break;
                case self::CODE_STATUS_NO_ACCESS:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): no access';
                    break;
                case self::CODE_STATUS_NO_DATA_FOUND:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): data not found';
                    $response = [];
                    break;
                case self::CODE_STATUS_REQUEST_ENTITY_TO_LARGE:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): request entity to large';
                    break;
                case self::CODE_STATUS_REQUEST_LIMIT_REACHED:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): request limit reached';
                    break;
                case self::CODE_STATUS_INTERNAL_SERVER_ERROR:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): internal server error';
                    break;
                case self::CODE_STATUS_METHOD_NOT_FOUND:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): method not found';
                    break;
                case self::CODE_STATUS_SERVICE_NOT_AVAILABLE:
                    $this->_error = 'Error (' . $this->getHttpCode() . '): service not available';
                    break;
                default:
                    $this->_error = 'Error (' . $this->getHttpCode() . ')';
                    break;
            }
        }
        return $response;
    }

/**
     * Get response
     *
     * @param string $requestEntity
     * @param array $params
     * @param string $ETag ETag
     * @return bool|array
     */
    public function request($requestEntity, array $params = [], $ETag = null)
    {
        $result = $this->getPureResponse($requestEntity, $params, $ETag);
        return $this->parseResponse($result);
    }
    /**
     * Return the user agent string
     * @return string
     */
    protected function getUserAgent()
    {
        return 'NationalCatalog PHP API client ' . self::VERSION;
    }

    /**
     * Return Url string
     * @param string $requestEntity
     * @return string
     */
    protected function getUrl($requestEntity)
    {
        return self::API_URL . '/' . self::VERSION . '/' . $requestEntity;
    }

    /**
     * Return list of brands
     *
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getBrands($ETag = null)
    {
        return $this->request(self::REQUEST_ENTITY_BRANDS, [], $ETag);
    }

    /**
     * Return list of brands
     *
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getLocations($partyId = null, $ETag = null)
    {
        $params = [];
        if (isset($partyId)) {
            $params = [
                'party_id' => $partyId
            ];
        }
        return $this->request(self::REQUEST_ENTITY_LOCATIONS, $params, $ETag);
    }

    /**
     * Return list of categories
     *
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getCategories($ETag = null)
    {
        return $this->request(self::REQUEST_ENTITY_CATEGORIES, [], $ETag);
    }

    public function getParties($role = null)
    {
        return $this->request(self::REQUEST_ENTITY_PARTIES, ['role' => $role], $ETag);
    }

    /**
     * Return list of products
     *
     * @param string $query
     * @return bool|array
     */
    public function getSuggestions($query)
    {
        $params = [
            'q' => $query
        ];
        return $this->request(self::REQUEST_ENTITY_SUGGESTIONS, $params);
    }

    /**
     * Get status of created feed
     *
     * @param int $feed_id feed id
     * @return bool|array
     */
    public function getFeedStatus($feed_id)
    {
        $params = [
            'feed_id' => $feed_id
        ];
        return $this->request(self::REQUEST_ENTITY_FEED_STATUS, $params);
    }

    /**
     * Get status of created feed
     *
     * @param int $feed_id feed id
     * @return bool|array
     */
    public function postFeed($content)
    {
        $params['apikey'] = $this->apiKey;
        $params['supplier_key'] = $this->supplierKey;
        $params['format'] = $this->format;
        $url = $this->getUrl($requestEntity) . '?' . http_build_query($params);
        ////"Content-Type: application/json";, "Content-Type: application/xml";
        $headers = ["Content-Type: application/json"];
        $result = $this->sendRequest($url, $content, $headers);
        return $this->parseResponse($result);
    }

    /**
     * Return list of attributes
     *
     * @param int $cat_id category id
     * @param int $attr_type attribute type (const)
     * @return bool|array
     */
    public function getAttributes($cat_id = null, $attr_type = null)
    {
        $params = [];
        if (isset($cat_id)) {
            $params['cat_id'] = $cat_id;
        }
        if (isset($attr_type)) {
            $params['attr_type'] = $attr_type;
        }
        return $this->request(self::REQUEST_ENTITY_ATTRIBUTES, $params);
    }

    /**
     * Return information about product by id
     *
     * @param int $good_id
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getProductById($good_id, $ETag = null)
    {
        $params = [
            'good_id' => $good_id
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by GTIN
     *
     * @param string $gtin
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getProductsByGtin($gtin, $ETag = null)
    {
        $params = [
            'gtin' => $gtin
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by LTIN
     *
     * @param string $ltin
     * @param int $party_id
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getProductsByLtin($ltin, $party_id, $ETag = null)
    {
        $params = [
            'ltin' => $ltin,
            'party_id' => $party_id
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, $params, $ETag);
    }

    /**
     * Return information about products by SKU
     *
     * @param string $sku
     * @param int $party_id
     * @param string $ETag ETag
     * @return bool|array
     */
    public function getProductsBySku($sku, $party_id, $ETag = null)
    {
        $params = [
            'sku' => $sku,
            'party_id' => $party_id
        ];
        return $this->request(self::REQUEST_ENTITY_PRODUCTS, $params, $ETag);
    }

    /**
     * Return array [ GoodId, ETag, Attributes ] for party
     *
     * @param int $party_id
     * @return bool|array
     */
    public function getETagsList($party_id)
    {
        $params = [
            'party_id' => $party_id
        ];
        return $this->request(self::REQUEST_ENTITY_ETAGS_LIST, $params);
    }
}