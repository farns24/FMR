<?php
require_once('DAO.php');
require_once('getFSXMLResponse.php');
require_once('FsFacade\FsFacade.php');
class FmrFactory
{
	private static $dao = null;
	
	private static $fsConnect = null;
	
	private static $searcher = null;
	
	private static $facade = null;
	
    public static function createDao()
    {
		if (!isset(static::$dao))
		{
			static::$dao = new DAO();
		}
        return static::$dao;
    }
	/**
	* Use to stub DAO component
	*/
	public static function setDao($inDao)
	{
		static::$dao = $inDao;
		
	}
	
	//FS COnnect
	 public static function createFsConnect()
    {
		if (!isset(static::$fsConnect ))
		{
			static::$fsConnect  = new FsConnect();
		}
        return static::$fsConnect ;
    }
	/**
	* Use to stub DAO component
	*/
	public static function setFsConnect($inFs)
	{
		static::$fsConnect  = $inFs;
		
	}
	
	 public static function createFsSearcher()
    {
		if (!isset(static::$fsConnect ))
		{
			
		}
        return static::$searcher ;
    }
	/**
	* Use to stub DAO component
	*/
	public static function setFsSearcher($inFs)
	{
		static::$searcher  = $inFs;
		
	}
	
	public static function getFacade()
	{
		if (!isset(static::$facade))
		{
			static::$facade = new FsFacade();
		}
		return static::$facade;
	}
}


?>