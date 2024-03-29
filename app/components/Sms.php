<?php
/**
 * Класс предназначен для отправки СМС.
 *
 * @version 1.6
 */

namespace components;

use models\SmsJobs;

/**
 * Class Sms
 *
 * @package helpers
 */
class Sms
{

    var $user = 'xxxx';   // ваш логин в системе
    var $pass = 'xxx';   // ваш пароль в системе
    var $hostname = 'beeline.amega-inform.ru';    // host замените на адрес сервера указанный в меню "Поддержка -> протокол HTTP"
    var $path = '/sendsms/';
    var $proxy_data = false;
    var $post_data = array();   // данные передаваемые на сервер
    var $multipost = false;   // множественный запрос по умолчанию false

    function __construct($user = false, $pass = false, $hostname = false, $proxy_data = false)
    {
        if ($user) {
            $this->user = $user;
        }
        if ($pass) {
            $this->pass = $pass;
        }
        if ($hostname) {
            $this->hostname = $hostname;
        }
        if ($proxy_data) {
            $this->proxy_data = $proxy_data;
        }
    }

    // команда на начало мульти запроса
    function start_multipost()
    {
        $this->multipost = true;
    }

    // сбор данных запроса
    function to_multipost($inv)
    {
        $this->post_data['data'][] = $inv;
    }

    // результирующий запрос на сервер и получение результата
    function process()
    {
        return $this->get_post_request($this->post_data);
    }

    ################# post_message
    // рассылка смс [mes] по телефонам [target] с возвратом результата XML

    function post_message($mes, $target, $sender = null)
    {
        if (is_array($target)) {
            $target = implode(',', $target);
        }
        return $this->post_mes($mes, $target, false, $sender);
    }

    // рассылка смс [mes] по кодовому имени контакт листа [phl_codename]
    function post_message_phl($mes, $phl_codename, $sender = null)
    {
        return $this->post_mes($mes, false, $phl_codename, $sender);
    }

    function post_mes($mes, $target, $phl_codename, $sender, $smstype = 'SENDSMS')
    {
        $in = array(
            'action'  => 'post_sms',
            'message' => $mes,
            'sender'  => $sender,
            'smstype' => $smstype
        );
        if ($target) {
            $in['target'] = $target;
        }
        if ($phl_codename) {
            $in['phl_codename'] = $phl_codename;
        }
        if ($this->multipost) {
            $this->to_multipost($in);
        } else {
            return $this->get_post_request($in);
        }
    }

    ################# status_sms
    /* 	получение стстуса смс
      допустимые параметры:
      1.	date_from
      date_to
      2.	sms_id
      3.	sms_group_id */

    function status_sms_id($sms_id)
    {
        return $this->status_sms(false, false, false, false, $sms_id);
    }

    function status_sms_group_id($sms_group_id)
    {
        return $this->status_sms(false, false, false, $sms_group_id, false);
    }

    function status_sms_date($date_from, $date_to, $smstype = 'SENDSMS')
    {
        return $this->status_sms($date_from, $date_to, $smstype, false, false);
    }

    function status_inbox($target = false, $unread = false, $date_from = false, $date_to = false)
    {
        if ($target) {
            $this->post_data['target'] = $target;
        }
        if ($unread) {
            $this->post_data['unread'] = $unread;
        }
        return $this->status_sms($date_from, $date_to, 'RECVSMS', false, false);
    }

    function status_sms($date_from, $date_to, $smstype, $sms_group_id, $sms_id)
    {
        $in = array('action' => ($smstype == "RECVSMS" ? "inbox" : "status"));
        //для входящих
        if (isset($this->post_data['target']) && $this->post_data['target']) {
            $in["phone"] = $this->post_data['target'];
        }
        if (isset($this->post_data['unread']) && $this->post_data['unread']) {
            $in["new_only"] = 1;
        }
        //для входящих
        if ($date_from) {
            $in['date_from'] = $date_from;
        }
        if ($date_to) {
            $in['date_to'] = $date_to;
        }
        if ($smstype) {
            $in['smstype'] = $smstype;
        }
        if ($sms_group_id) {
            $in['sms_group_id'] = $sms_group_id;
        }
        if ($sms_id) {
            $in['sms_id'] = (string)$sms_id;
        }
        if ($this->multipost) {
            $this->to_multipost($in);
        } else {
            return $this->get_post_request($in);
        }
    }

    ################################################
    // запрос на сервер и получение результата

    function get_post_request($invars)
    {
        $invars['user'] = ($this->user);
        $invars['pass'] = ($this->pass);
        $invars['CLIENTADR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        $invars['HTTP_ACCEPT_LANGUAGE'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE']
            : false;
        $PostData = http_build_query($invars);
        $len = strlen($PostData);
        $nn = "\r\n";

        //работаем с прокси
        $fsock_proxy = false;
        $proxy_auth = false;
        if ($this->proxy_data) {
            $pd = $this->proxy_data;
            if (!$pd["host"] || $pd["host"] == "") {
                return 'Не указан адрес прокси-сервера';
            } else {
                $host = $pd["host"];
            }
            $port = isset($pd["port"]) && $pd["port"] != '' ? $pd["port"] : 80;
            $user = isset($pd["user"]) && $pd["user"] != '' ? $pd["user"] : false;
            $pass = isset($pd["pass"]) && $pd["pass"] != '' ? $pd["pass"] : false;
            if (($fsock_proxy = @fsockopen($host, $port, $errno, $errstr, 30)) !== false) {
                if ($user) {
                    $proxy_auth = "Proxy-Authorization: Basic " . base64_encode("$user:$pass") . $nn;
                }
            } else {
                return 'Невозможно соединиться с прокси-сервером.';
            }
        }

        $send = "POST http://" . $this->hostname . $this->path . " HTTP/1.0" . $nn . "Host: " . $this->hostname . ":80"
            . $nn . ($proxy_auth ? $proxy_auth : "") . "Content-Type: application/x-www-form-urlencoded" . $nn . "Content-Length: $len"
            . $nn . "User-Agent: AISMS PHP class" . $nn . $nn . $PostData;
        flush();
        $fp = $fsock_proxy ? $fsock_proxy : @fsockopen($this->hostname, 80, $errno, $errstr, 30);
        //if(($fp = @fsockopen($this->hostname, 80, $errno, $errstr, 30))!==false) {
        if ($fp !== false) {
            fputs($fp, $send);
            $header = '';
            do {
                $header .= fgets($fp, 4096);
            } while (strpos($header, "\r\n\r\n") === false);
            if (get_magic_quotes_runtime()) {
                $header = $this->decode_header(stripslashes($header));
            } else {
                $header = $this->decode_header($header);
            }

            $body = '';
            while (!feof($fp)) {
                $body .= fread($fp, 8192);
            }
            if (get_magic_quotes_runtime()) {
                $body = $this->decode_body($header, stripslashes($body));
            } else {
                $body = $this->decode_body($header, $body);
            }

            fclose($fp);
            return $body;
        } else {
            return 'Невозможно соединиться с сервером.';
        }
    }

    function decode_header($str)
    {
        $part = preg_split("/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY);
        $out = array();
        for ($h = 0; $h < sizeof($part); $h++) {
            if ($h != 0) {
                $pos = strpos($part[$h], ':');
                $k = strtolower(str_replace(' ', '', substr($part[$h], 0, $pos)));
                $v = trim(substr($part[$h], ($pos + 1)));
            } else {
                $k = 'status';
                $v = explode(' ', $part[$h]);
                $v = $v[1];
            }
            if ($k == 'set-cookie') {
                $out['cookies'][] = $v;
            } else {
                if ($k == 'content-type') {
                    if (($cs = strpos($v, ';')) !== false) {
                        $out[$k] = substr($v, 0, $cs);
                    } else {
                        $out[$k] = $v;
                    }
                } else {
                    $out[$k] = $v;
                }
            }
        }
        return $out;
    }

    function decode_body($info, $str, $eol = "\r\n")
    {
        $tmp = $str;
        $add = strlen($eol);
        if (isset($info['transfer-encoding']) && $info['transfer-encoding'] == 'chunked') {
            $str = '';
            do {
                $tmp = ltrim($tmp);
                $pos = strpos($tmp, $eol);
                $len = hexdec(substr($tmp, 0, $pos));
                if (isset($info['content-encoding'])) {
                    $str .= gzinflate(substr($tmp, ($pos + $add + 10), $len));
                } else {
                    $str .= substr($tmp, ($pos + $add), $len);
                }
                $tmp = substr($tmp, ($len + $pos + $add));
                $check = trim($tmp);
            } while (!empty($check));
        } elseif (isset($info['content-encoding'])) {
            $str = gzinflate(substr($tmp, 10));
        }
        return $str;
    }

    /**
     * Отправка SMS.
     */
    public static function send($phone, $message, $subject = null)
    {
        if (empty($phone) || empty($message)) {
            return false;
        }

        return (
        new self(
            \Config::get('sms.login'),
            \Config::get('sms.password')
        )
        )->post_message($message, $phone, $subject === null ? \Config::get('sms.subject') : $subject);
    }

    /**
     * Поставит задание на отправку смс.
     *
     * @param       $phone
     * @param       $message
     * @param array $acceptedTime
     * @param null  $subject
     *
     * @return bool
     */
    public static function addJob($phone, $message, $acceptedTime = [0, 0], $subject = null)
    {
        $job = new SmsJobs;
        $job->setAttributes(
            [
                'phone'         => $phone,
                'message'       => $message,
                'accepted_time' => $acceptedTime[0] . ':' . $acceptedTime[1],
            ]
        );
        return $job->save();
    }
}


if (!function_exists('http_build_query')) {

    function http_build_query($data, $prefix = null, $sep = '', $key = '')
    {
        $ret = array();
        foreach ((array)$data as $k => $v) {
            $k = urlencode($k);
            if (is_int($k) && $prefix != null) {
                $k = $prefix . $k;
            }
            if (!empty($key)) {
                $k = $key . "[" . $k . "]";
            }

            if (is_array($v) || is_object($v)) {
                array_push($ret, http_build_query($v, "", $sep, $k));
            } else {
                array_push($ret, $k . "=" . urlencode($v));
            }
        };
        if (empty($sep)) {
            $sep = ini_get("arg_separator.output");
        }
        return implode($sep, $ret);
    }
};
?>