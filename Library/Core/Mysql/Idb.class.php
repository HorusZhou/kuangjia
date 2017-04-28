<?php
interface Idb{
	public function add( $_table, $data);
	public function delete( $_table , $_where);
	public function update( $_table ,$_fields ,$_where);
	public function get( $_table , $_fields , $_where );
	public function getList( $_table , $_fields , $_where , $_orderby , $_limit );
	public function count( $_table , $_where );
}

?>