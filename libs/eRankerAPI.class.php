<?PHP

/**
 * Class eRanker API. This class will provide a easy access to eRanker API.
 * You need an API key to use this class. Conect with eRaker Api (http://api.eranker.com).
 * This class need php-curl to work.
 * USAGE:
 *      $erapi = new eRankerAPI($email, $apikey);
 *      $account = $erapi->account();
 *      $newreport = $erapi->reportnew('http://www.eranker.com', array('title', 'meta_description'));
 *      $reports = $erapi->reports(1, 10);
 *      $reportdata = $erapi->reportdata(1);
 * @author Renan Gomes
 * @since 27-01-2014
 * @version 1.0
 */
if (!class_exists("eRankerAPI")) {

    class eRankerAPI {

        /**
         * The email used to login into the eRanker API
         * @var string
         */
        public $email = null;

        /**
         * The API key used to login into the eRanker API. (get one at: http://www.eranker.com)
         * @var string
         */
        private $apikey = null;

        /**
         * The API base URL
         * @var string
         */
        private $apiurl = "http://api.eranker.com/";

        /**
         * The cache folder.
         * @var string
         */
        private $cachefolder = null;

        /**
         * If this variable is true, we will use the cache system
         * @var bool 
         */
        private $cache = true;

        /**
         * Time to keep the sucessfully downloaded data into cache (reports with status DONE, for example)
         * @var int 
         */
        private $cachetime = 60;

        /**
         * Set the conection mode for the eRanker API interaction
         * @param string The account email
         * @param string the account API key (get one at: http://www.eranker.com)
         * @param bool $cache set this to TRUE if you want this class to enable a cache
         * @param int $cachetime Time to keep the downloaded data in cache
         * @param string $cachefolder The Cache folder path
         */
        public function eRankerAPI($email, $apikey, $cache = true, $cachetime = 60, $cachefolder = null) {
            $this->email = $email;
            $this->apikey = strtolower($apikey);
            if (!empty($cachefolder)) {
                $this->cachefolder = $cachefolder;
            } else {
                $this->cachefolder = sys_get_temp_dir() . "/";
            }
            $this->cachetime = $cachetime;
            $this->cache = $cache;
        }

        /**
         * Returns the current account information. Email, Credits, Plans, etc.
         * @return FALSE|object The current account details as an object. Return FALSE if failed.
         */
        public function account() {
            $ret = $this->download($this->apiurl . '/account/info.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Return an Array with the list of supported countries.
         * @return FALSE|array The country array. Return FALSE if failed.
         * An Error Object can be returned if the code is invalid. 
         */
        public function countries() {
            $ret = $this->download($this->apiurl . '/country/list.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Return the country object that has the code.
         * @param string $code The ISO country code
         * @return FALSE|object The country object. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function country($code) {
            $ret = $this->download($this->apiurl . '/country/' . strtolower(trim($code)) . '.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Return an Array with the list of supported languages.
         * @return FALSE|array The languages array. Return FALSE if failed.
         * An Error Object can be returned if the code is invalid. 
         */
        public function languages() {
            $ret = $this->download($this->apiurl . '/language/list.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Return the language object that has the code.
         * @param string $code The ISO language code
         * @return FALSE|object The language object. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function language($code) {
            $ret = $this->download($this->apiurl . '/language/' . strtolower(trim($code)) . '.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Return an Array with the list of all factors from the system.
         * @param string $language The ISO language code that you want to get the texts from the factor
         * You can download the language list using the comand languages and check the ones that has the translation available.
         * @return FALSE|array The factors array using the specified language. Return FALSE if failed.
         * An Error Object can be returned if the code is invalid. 
         */
        public function factors($language = 'en') {
            if (empty($language)) {
                $language = 'en';
            }
            $ret = $this->download($this->apiurl . '/factor/list.json?' . http_build_query(array('language' => $language, 'email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * This function is used only by the eRanker plugin. This function saves the user log for the wp plugin.
         * @param string $domain The domain to log
         * @param string $url The url to log
         * @param string $action The action done
         * @param string $email_log The ovewrite email to log
         * @return FALSE|object The log data. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function pluginlog($domain, $url, $action, $email_log = null) {
            if (empty($domain) || empty($url) || empty($action)) {
                return false;
            }
            $post_fields = array();
            $post_fields['domain'] = (empty($domain)) ? '' : $domain;
            $post_fields['url'] = (empty($url)) ? '' : $url;
            if (!empty($action) && (strcasecmp($action, 'ACTIVATE') === 0 || strcasecmp($action, 'DEACTIVATE') === 0 || strcasecmp($action, 'LINK') === 0 || strcasecmp($action, 'UNLINK') === 0)) {
                $post_fields['action'] = $action;
            } else {
                return false;
            }
            if (!empty($this->email)) {
                $post_fields['email'] = $this->email;
            } else {
                $post_fields['email'] = $email_log;
            }
            $ret = $this->download($this->apiurl . '/plugin/log.json?', 'POST', 30, array(), json_encode((object) $post_fields));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Read a report by id. The report data will contains the basic report information. Including the status and factors_avaiable.
         * This function can be used to monitor if a report is done or not. Avoid call this function too many times too quick.
         * @param integer $id This is the report id that will be downloaded. Must be a number > 0.
         * @return FALSE|object The report object. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function report($id) {
            if ($id === null || $id === "" || $id === 0 || !is_numeric($id)) {
                return false;
            }
            $ret = $this->download($this->apiurl . '/report/' . $id . '.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Read the list of user reports page by page.
         * @param integer $page This is the page you need to download. Default=1. Must be a number > 0.
         * @param integer $itemsperpage The number of reports per page. Default=10. Must be a number >= 1 and <=50.
         * @return FALSE|array The array with the report objects. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function reports($page = 1, $itemsperpage = 10) {
            $page = max(1, (int) $page);
            $itemsperpage = min(50, max(1, (int) $itemsperpage));

            $ret = $this->download($this->apiurl . '/report/list.json?' . http_build_query(array('page' => $page, 'itemsperpage' => $itemsperpage, 'email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Get the report simple data WITHOUT scores or factor text.
         * Use this function if your software only need the data from each factor on the report.
         * @param integer $id This is the report id that will be downloaded. Must be a number > 0.
         * @return FALSE|array The report data with scores and texts in the specified language. Return FALSE if failed.
         * An Error Object can be returned if the code is invalid. 
         */
        public function reportdata($id) {
            if ($id === null || $id === "" || $id === 0 || !is_numeric($id)) {
                return false;
            }
            $ret = $this->download($this->apiurl . '/report/' . $id . '/data.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Get the report factor data, scores and texts on the language used.
         * Please not that this is a resource intensive function, please do not call it too quick or we will start delaying the server requests.
         * @param integer $id This is the report id that will be downloaded. Must be a number > 0.
         * @param string $language The ISO language code that you want to get the texts from the factor
         * You can download the language list using the comand languages and check the ones that has the translation available.
         * @return FALSE|array The report data with scores and texts in the specified language. Return FALSE if failed.
         * An Error Object can be returned if the code is invalid. 
         */
        public function reportscores($id, $language = 'en') {
            if ($id === null || $id === "" || $id === 0 || !is_numeric($id)) {
                return false;
            }
            if (empty($language)) {
                $language = 'en';
            }
            $ret = $this->download($this->apiurl . '/report/' . $id . '/scores.json?' . http_build_query(array('language' => $language, 'email' => $this->email, 'apikey' => $this->apikey)));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * 
         * @param string $url The URL that will be used on the report. Must be a valid URL.
         * If this URL is empty or NULL, this function fails and return FALSE.
         * @param array $factors Array with a list of valid factors. Invalid factors will be ingnored. 
         * If this list is empty or NULL, this function fails and return FALSE.
         * @return FALSE|object The created report object. Return FALSE if failed. 
         * An Error Object can be returned if the code is invalid. 
         */
        public function reportnew($url, $factors) {
            if (empty($url)) {
                return false;
            }
            if (empty($factors)) {
                return false;
            }
            $post_fields = array();
            $post_fields['url'] = trim($url);
            $post_fields['factors'] = $factors;
            $ret = $this->download($this->apiurl . '/report/new.json?' . http_build_query(array('email' => $this->email, 'apikey' => $this->apikey)), 'POST', 30, array(), json_encode((object) $post_fields));
            if (!empty($ret['content'])) {
                $responseobj = json_decode(trim($ret['content']));
                return $responseobj;
            }
            return false;
        }

        /**
         * Do the download of a request and return the result headers, content and more info.
         * @param string $url The url to download
         * @param string $method The HTTP method to use
         * @param integer $timeout The execution timeout
         * @param array $options The options for the requests
         * @param string $post_fields The data to be send (if is a post). a JSON data
         * @return array An array with the information about the requests. 
         * Array keys are: headers, content, info
         */
        private function download($url, $method = 'GET', $timeout = 30, $options = array(), $post_fields = '') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Content-length: ' . strlen($post_fields)));
            }
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $header_size);
            $content = substr($response, $header_size);
            $theinfo = curl_getinfo($ch);
            curl_close($ch);
            $outarray = array('headers' => $headers, 'content' => $content, 'info' => $theinfo);
            //echo "<pre>" . print_r($outarray, true) . "</pre>";
            return $outarray;
        }

    }

}
    