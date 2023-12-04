<?php
class IBSng
{
    public $error;
    public $username;
    public $password;
    public $ip;

    private $handler;
    private $cookie;
    private  $maxredirect;

    public function __construct($username, $password, $ip)
    {
        $this->username = $username;
        $this->password = $password;
        $this->ip = $ip;
        $this->maxredirect = 5;

        $url = $this->ip . '/IBSng/admin/';
        $this->handler = curl_init();

        $post_data['username'] = $username;
        $post_data['password'] = $password;

        curl_setopt($this->handler, CURLOPT_URL, $url);
        curl_setopt($this->handler, CURLOPT_POST, true);
        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($this->handler, CURLOPT_HEADER, true);
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handler, CURLOPT_COOKIEJAR, $this->cookie);

        $mr = $this->maxredirect === null ? 5 : intval($this->maxredirect);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($this->handler, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, false);
            if ($mr > 0) {
                $newurl = curl_getinfo($this->handler, CURLINFO_EFFECTIVE_URL);

                $rch = curl_copy_handle($this->handler);
                curl_setopt($this->handler, CURLOPT_URL, $url);
                curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $newurl = trim(array_pop($matches));
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);
                curl_close($rch);
                if (!$mr) {
                    if ($this->maxredirect === null) {
                        trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                    } else {
                        $maxredirect = 0;
                    }
                    return false;
                }
                curl_setopt($this->handler, CURLOPT_URL, $newurl);
            }
        }

        $output = curl_exec($this->handler);

        preg_match_all('|Set-Cookie: (.*);|U', $output, $matches);
        $this->cookie = implode('; ', $matches[1]);
    }

    public function removeUser($user_ids)
	{
			$url 									= $this->ip . '/IBSng/admin/user/del_user.php';
			$post_data['user_id'] 					= $user_ids;
			$post_data['delete'] 					= '1';
			$post_data['delete_comment']            = '';
			$post_data['delete_connection_logs']    = 'on';
			$post_data['delete_audit_logs']         = 'on';

			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);

			$output = curl_exec($this->handler);

			if (strpos($output, 'Deleted Successfully') !== false)
			{
				echo "Success<br />";
			} else {
				echo "Remove user error<br />";
			}
	}
}
