<?php

/**
 * Connect your PHP code to your copy of PhantomBot
 * Copyright (C) 2016 Juraji
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
class PhantomBotConnector
{
    /* @var resource $curl */
    private $curl;
    /* @var string $botAddress */
    private $botAddress;
    /* @var int $botPort */
    private $botPort;
    /* @var string $botOauth */
    private $botOauth;

    /**
     * PhantomBotConnector constructor.
     * @param string $address
     * @param string $oauth
     * @param int $port
     */
    public function PhantomBotConnector($address, $oauth, $port = 25000)
    {
        /** SETTINGS **/
        /**
         * botAddress is the address corresponding to the direct IP address of your copy of PhantomBot.
         * If you have PhantomBot running in your local network this would be the IP address of the machine.
         * If you have PhantomBot running externally this would be the external IP address of the machine.
         */
        $this->botAddress = $address;
        /**
         * botPort is the port on wich PhantomBot is listening for HTTP requests.
         * Normally this will be 25000. Only change this if you've changed the port on wich PhantomBot should listen.
         */
        $this->botPort = $port;
        /**
         * botOauth must be the Oauth token you supplied while setting up PhantomBot.
         * This Oauth token can be found in botlogin.txt, within the installation folder of PhantomBot.
         */
        $this->botOauth = $oauth;
    }

    /**
     * Get all records from a table in PhantomBot.
     * Ex: "getTable('points')" would return all rows in the points table
     *
     * @param string $fileName
     * @return array
     */
    public function getTable($fileName)
    {
        if (substr($fileName, 0, 10) != '/inistore/') {
            $fileName = '/inistore/' . $fileName;
        }

        $this->init($fileName);

        $result = curl_exec($this->curl);
        $errNo = curl_errno($this->curl);
        $errMsg = curl_error($this->curl);
        $status = curl_getinfo($this->curl);

        $this->close();

        return [$this->splitFile($result, true), $status, $errNo, $errMsg];
    }

    /**
     * Get a file from the "addons" folder as array.
     * Ex: "getAddonFile('youtubePlayer/currentsong.txt')" would return all lines in ./addons/youtubePlayer/currensong.txt
     *
     * @param string $filePath
     * @return array
     */
    public function getAddonFile($filePath)
    {
        if (substr($filePath, 0, 1) == '/') {
            $filePath = substr($filePath, 1);
        }

        if (substr($filePath, 0, 7) != 'addons/') {
            $filePath = '/addons/' . $filePath;
        }

        $this->init($filePath);

        $result = curl_exec($this->curl);
        $errNo = curl_errno($this->curl);
        $errMsg = curl_error($this->curl);
        $status = curl_getinfo($this->curl);

        $this->close();

        return [$this->splitFile($result), $status, $errNo, $errMsg];
    }

    /**
     * Get a file from the "web" folder as array.
     * Ex: "getWebFile('currentsong.txt')" would return all lines in ./web/currensong.txt
     *
     * @param string $filePath
     * @return array
     */
    public function getWebFile($filePath)
    {
        if (substr($filePath, 0, 1) == '/') {
            $filePath = substr($filePath, 1);
        }

        if (substr($filePath, 0, 7) != 'web/') {
            $filePath = '/web/' . $filePath;
        }

        $this->init($filePath);

        $result = curl_exec($this->curl);
        $errNo = curl_errno($this->curl);
        $errMsg = curl_error($this->curl);
        $status = curl_getinfo($this->curl);

        $this->close();

        return [$this->splitFile($result), $status, $errNo, $errMsg];
    }

    /**
     * Get a file from the "logs" folder as array.
     * Ex: "getLogFile('logfile.txt')" would return all lines in ./logs/logfile.txt
     *
     * @param string $filePath
     * @return array
     */
    public function getLogFile($filePath)
    {
        if (substr($filePath, 0, 1) == '/') {
            $filePath = substr($filePath, 1);
        }

        if (substr($filePath, 0, 7) != 'logs/') {
            $filePath = '/logs/' . $filePath;
        }

        $this->init($filePath);

        $result = curl_exec($this->curl);
        $errNo = curl_errno($this->curl);
        $errMsg = curl_error($this->curl);
        $status = curl_getinfo($this->curl);

        $this->close();

        return [$this->splitFile($result), $status, $errNo, $errMsg];
    }

    private function close()
    {
        curl_close($this->curl);
    }

    /**
     * @param string $filePath
     */
    private function init($filePath)
    {
        $this->curl = curl_init($this->botAddress . $filePath);
        curl_setopt($this->curl, CURLOPT_PORT, $this->botPort);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Chrome/44.0.2403.52 PhantomBotConnector/1.0');
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('password: ' . str_replace('oauth:', '', $this->botOauth)));

        if (defined('CURLOPT_IPRESOLVE')) {
            curl_setopt($this->curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
    }

    /**
     * @param string $file
     * @param bool $isIni
     * @return array
     */
    private function splitFile($file, $isIni = false)
    {
        if ($file == '') {
            return [];
        }

        $splitFile = preg_split('/\n/', trim($file));
        $result = [];

        foreach ($splitFile as $line) {
            $splitLine = explode('=', $line);

            if ($isIni) {
                $result[$splitLine[0]] = $splitLine[1];
            } else {
                $result[] = $line;
            }
        }

        return $result;
    }
}