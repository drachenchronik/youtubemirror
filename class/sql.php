<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

class sql
{
	private static $database = false;
	
	public static function connect()
	{
		if (!function_exists('mysqli_connect'))
		{
			trigger_error('No MySQLi Support', E_USER_ERROR);
		}
		if (!self::$database = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWD, SQL_DBNAME, (int) SQL_PORT))
		{
			trigger_error('Database conection failed', E_USER_ERROR);
		}
		mysqli_set_charset ( self::$database , 'utf8');
	}
	
	
	public static function fetch_array($result)
	{
		return mysqli_fetch_array($result);
	}

	public static function query($sql)
	{
		$result = mysqli_query(self::$database, $sql);
		if (!$result)
		{
			trigger_error(mysqli_error(self::$database), E_USER_ERROR);
		}
		return $result;
	}
	
	public static function sql_escape($sql)
	{
		return mysqli_real_escape_string(self::$database, $sql);
	}
	
	public static function insert_array($table, $array)
	{
		$sql = 'INSERT INTO ' . self::sql_escape($table) . ' ' . self::sql_build_array('INSERT', $array);
		return self::query($sql);
	}
	
	public static function sql_build_array($query, $assoc_ary = false)
	{
		if (! is_array($assoc_ary))
		{
			return false;
		}

		$fields = $values = array();
		if ($query == 'INSERT' || $query == 'INSERT_SELECT')
		{
			foreach($assoc_ary as $key => $var)
			{
				$fields[] = $key;
				if (is_array($var) && is_string($var[0]))
				{
					$values[] = $var[0];
				}
				else
				{
					$values[] = self::_sql_validate_value($var);
				}
			}
			$query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
		}
		else
			if ($query == 'UPDATE' || $query == 'SELECT')
			{
				$values = array();
				foreach($assoc_ary as $key => $var)
				{
					$values[] = "$key = " . self::_sql_validate_value($var);
				}
				$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
			}

		return $query;
	}
	
	public static function _sql_validate_value($var)
	{
		if (is_null($var))
		{
			return 'NULL';
		}
		else
		{
			if (is_string($var))
			{
				return "'" . self::sql_escape($var) . "'";
			}
			else if (is_float($var))
			{
				return str_replace(',', '.', $var);
			}
			else
			{
				return (is_bool($var)) ? intval($var) : $var;
			}
		}
	}
	
}