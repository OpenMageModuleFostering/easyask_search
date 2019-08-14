<?php
// Create a new RemoteEasyAsk instance on website startup
class EasyAsk_Impl_RemoteFactory
{
	public static function create($hostName, $port, $dictionary){
		return new EasyAsk_Impl_RemoteEasyAsk($hostName, $port, $dictionary);		
	}
}
?>