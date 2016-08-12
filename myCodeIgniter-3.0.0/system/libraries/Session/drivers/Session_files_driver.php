<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
*/
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Files Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_files_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Save path
	 *
	 * @var	string
	 */
	protected $_save_path;

	/**
	 * File handle
	 *
	 * @var	resource
	 */
	protected $_file_handle;

	/**
	 * File name
	 *
	 * @var	resource
	 */
	protected $_file_path;

	/**
	 * File new flag
	 *
	 * @var	bool
	 */
	protected $_file_new;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		// 设置 保存路径 
		if (isset($this->_config['save_path']))
		{	
			// 取config 中 save_path 并设置php.ini
			$this->_config['save_path'] = rtrim($this->_config['save_path'], '/\\');
			ini_set('session.save_path', $this->_config['save_path']);
		}
		else
		{
			// 取 session.save_path  当做save_path
			$this->_config['save_path'] = rtrim(ini_get('session.save_path'), '/\\');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 *
	 * Sanitizes the save_path directory.
	 *
	 * @param	string	$save_path	Path to session files' directory
	 * @param	string	$name		Session cookie name
	 * @return	bool
	 */
	// 检查 save_path 的可读性  设置 file_path
	public function open($save_path, $name)
	{

		// 检查权限 不存在尝试创建目录
		if ( ! is_dir($save_path))
		{

			if ( ! mkdir($save_path, 0700, TRUE))
			{
				throw new Exception("Session: Configured save path '".$this->_config['save_path']."' is not a directory, doesn't exist or cannot be created.");
			}
		}
		elseif ( ! is_writable($save_path))
		{
			throw new Exception("Session: Configured save path '".$this->_config['save_path']."' is not writable by the PHP process.");
		}

		$this->_config['save_path'] = $save_path;

		// 设置文件路径  保存路径+$name[+ md5(ip)]
		$this->_file_path = $this->_config['save_path'].DIRECTORY_SEPARATOR
			.$name // we'll use the session cookie name as a prefix to avoid collisions
			.($this->_config['match_ip'] ? md5($_SERVER['REMOTE_ADDR']) : '');
			// echo $_SERVER['REMOTE_ADDR'];
		//echo $this->_file_path;

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	
	// 读取session 内容  首次读取 创建新文件
	public function read($session_id)
	{
		// echo $this->_file_path;   echo '<br>';echo $session_id;
		// This might seem weird, but PHP 5.6 introduces session_reset(),
		// which re-reads session data
		if ($this->_file_handle === NULL)
		{
			// Just using fopen() with 'c+b' mode would be perfect, but it is only
			// available since PHP 5.2.6 and we have to set permissions for new files,
			// so we'd have to hack around this ...
			
			// 当文件不存在时 尝试创建文件 并 设置文件标识_file_new 为true
			if (($this->_file_new = ! file_exists($this->_file_path.$session_id)) === TRUE)
			{
				if (($this->_file_handle = fopen($this->_file_path.$session_id, 'w+b')) === FALSE)
				{
					log_message('error', "Session: File '".$this->_file_path.$session_id."' doesn't exist and cannot be created.");
					return FALSE;
				}
			}
			// 文件存在 创建当前句柄
			elseif (($this->_file_handle = fopen($this->_file_path.$session_id, 'r+b')) === FALSE)
			{
				log_message('error', "Session: Unable to open file '".$this->_file_path.$session_id."'.");
				return FALSE;
			}

			// 锁定文件失败操作
			if (flock($this->_file_handle, LOCK_EX) === FALSE)
			{
				log_message('error', "Session: Unable to obtain lock for file '".$this->_file_path.$session_id."'.");
				fclose($this->_file_handle);
				$this->_file_handle = NULL;
				return FALSE;
			}

			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;

			// 新文件 权限设置 /创建指纹
			if ($this->_file_new)
			{
				chmod($this->_file_path.$session_id, 0600);
				$this->_fingerprint = md5('');
				return '';
			}
		}
		else
		{
			//重置指针 将 handle 的文件位置指针设为文件流的开头。 
			rewind($this->_file_handle);
		}

		$session_data = '';

		// 读取整个文件
		for ($read = 0, $length = filesize($this->_file_path.$session_id); $read < $length; $read += strlen($buffer))
		{
			if (($buffer = fread($this->_file_handle, $length - $read)) === FALSE)
			{
				break;
			}

			$session_data .= $buffer;
		}

		//指纹 加密信息
		$this->_fingerprint = md5($session_data);

		return $session_data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 *
	 * @par am	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		// If the two IDs don't match, we have a session_regenerate_id() call
		// and we need to close the old handle and open a new one
		
		// 验证当前session_id 
		if ($session_id !== $this->_session_id && ( ! $this->close() OR $this->read($session_id) === FALSE))
		{
			return FALSE;
		}

		if ( ! is_resource($this->_file_handle))
		{
			return FALSE;
		}
		elseif ($this->_fingerprint === md5($session_data))
		{
			return ($this->_file_new)
				? TRUE
				: touch($this->_file_path.$session_id);
		}

		if ( ! $this->_file_new)
		{
			// 将文件截断到给定的长度
			ftruncate($this->_file_handle, 0);
			// 重置指针
			rewind($this->_file_handle);
		}

		if (($length = strlen($session_data)) > 0)
			
		{
			for ($written = 0; $written < $length; $written += $result)
			{
				if (($result = fwrite($this->_file_handle, substr($session_data, $written))) === FALSE)
				{
					break;
				}
			}

			if ( ! is_int($result))
			{
				$this->_fingerprint = md5(substr($session_data, 0, $written));
				log_message('error', 'Session: Unable to write data.');
				return FALSE;
			}
		}

		$this->_fingerprint = md5($session_data);
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes file descriptor.
	 *
	 * @return	bool
	 */
	public function close()
	{
		// 文件句柄 是否 打开
		if (is_resource($this->_file_handle))
		{
			// 解锁文件  关闭句柄
			flock($this->_file_handle, LOCK_UN);
			fclose($this->_file_handle);

			// 清空句柄 新文件标识 和 当前_session_id
			$this->_file_handle = $this->_file_new = $this->_session_id = NULL;
			return TRUE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	
	// 文件销毁
	public function destroy($session_id)
	{
		if ($this->close())
		{
			return file_exists($this->_file_path.$session_id)
				? (unlink($this->_file_path.$session_id) && $this->_cookie_destroy())
				: TRUE;
		}
		elseif ($this->_file_path !== NULL)
		{
			// 清除文件缓存状态
			clearstatcache();
			return file_exists($this->_file_path.$session_id)
				? (unlink($this->_file_path.$session_id) && $this->_cookie_destroy())
				: TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	
	// 垃圾回收器
	public function gc($maxlifetime)
	{

		// 
		if ( ! is_dir($this->_config['save_path']) OR ($directory = opendir($this->_config['save_path'])) === FALSE)
		{
			log_message('debug', "Session: Garbage collector couldn't list files under directory '".$this->_config['save_path']."'.");
			return FALSE;
		}

		// 当前 session 有效时期分割线
		$ts = time() - $maxlifetime;


		// 匹配 session 文件     非匹配IP:  /^ci_session[0-9][a-f]{40}$/  匹配IP /^ci_session[0-9][a-f]{72}$/
		$pattern = sprintf(
			'/^%s[0-9a-f]{%d}$/',
			preg_quote($this->_config['cookie_name'], '/'),
			($this->_config['match_ip'] === TRUE ? 72 : 40)
		);


		while (($file = readdir($directory)) !== FALSE)
		{
			// If the filename doesn't match this pattern, it's either not a session file or is not ours
			// 匹配 ci_session文件格式
			if ( ! preg_match($pattern, $file)
				// 文件有效性验证
				OR ! is_file($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)
				// 取得文件修改时间
				OR ($mtime = filemtime($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)) === FALSE
				// 如果修改时间>分割线
				OR $mtime > $ts)
			{
				continue;
			}

			unlink($this->_config['save_path'].DIRECTORY_SEPARATOR.$file);
		}

		closedir($directory);

		return TRUE;
	}

}
