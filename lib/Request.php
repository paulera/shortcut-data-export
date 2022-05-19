<?php namespace app\lib;

use app\Config;

class Request
{

    private $curlHandler;

    function __construct($url)
    {
        $this->curlHandler = curl_init($url);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER,
            array (
                'Content-Type: application/json',
                'Shortcut-Token: ' . Config::apikey
            )
        );
    }

    /**
     *
     * Adds POST data to a cUrl handler
     *
     * @param $postData array Associative array containing the data to post
     * @return void
     */
    private function setPostData(array $postData) : void {
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    private function execRequest() {
        $result = curl_exec($this->curlHandler);

        if (curl_error($this->curlHandler)) {
            $output = array(
                'message' => curl_error($this->curlHandler)
            );
        } else {
            $output = json_decode($result, true);
        }

        curl_close($this->curlHandler);

        return $output;
    }

    public function post($fields)
    {
        $this->setPostData($fields);
        return $this->execRequest();
    }

    public function get()
    {
        return $this->execRequest();
    }



    // -------------------------------

    // THE FUNCTIONS BELOW STILL NEED REFATORING


    function getRequest($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array (
                'Content-Type: application/json',
                'Shortcut-Token: ' . Config::apikey
            )
        );
        $result = curl_exec($ch);

        if (curl_error($ch)) {
            $output = array(
                'message' => curl_error($ch)
            );
        } else {
            $output = json_decode($result, true);
        }

        curl_close($ch);

        return $output;

    }

    static function getPaginatedRequest($url)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array (
                'Content-Type: application/json',
                'Shortcut-Token: ' . Config::apikey
            )
        );

        $result = curl_exec($ch);

        if (curl_error($ch)) {
            $output = array(
                'message' => curl_error($ch)
            );
        } else {
            $output = json_decode($result, true);

            if ($output['next']) {
                $nextUrl = Config::host . $output['next'];
                $nextOutput = self::getPaginatedRequest($nextUrl);

                // incorporates the results of the next page into the current one
                if ($nextOutput['next']) {
                    $output['next'] = $nextOutput['next'];
                } else {
                    $output['next'] = null;
                }

                $output['data'] = array_merge($output['data'], $nextOutput['data']);

            }
        }

        curl_close($ch);

        return $output;

    }

    function searchStoriesByQuery($query)
    {
        $data = array(
            'page_size' => 25,
            'query' => $query
        );
        $url = Config::host . Config::endpoint . '/search/stories' . '?' . http_build_query($data);

        $output = self::getPaginatedRequest($url);

        $storyDataMap = array(
            'id',
            'story_type',
            'estimate',
            'deadline',
            'name',
            'started',
            'started_at',
            'completed',
            'completed_at',
            'blocker',
            'blocked'
        );

        print(implode("\t", $storyDataMap));

        foreach ($output['data'] as $index => $data) {
            $row = array();
            foreach ($storyDataMap as $key) {
                $value = $data[$key];
                if (gettype($value) == 'boolean') {
                    $value = $value ? 1 : 0;
                } elseif (substr($key, -3) == "_at") {
                    $value = substr($value, 0, 10);
                }
                $row[] = $value;
            }
            print("\n" . implode("\t", array_values($row)));
        }

    }

}