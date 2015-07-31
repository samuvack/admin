<?php
	class Property 
	{
		private $id;
		private $name;
		private $description;
		private $datatype;
		private $descr;
		
        function __construct($id = null, $name, $description, $datatype, $descr=null)
        {
            $this->id = $id;
			$this->name = $name;
            $this->description = $description;
			$this->datatype = $datatype;
			$this->descr = $descr;
        }

		function setId($new_Id)
		{
			$this->id = (int) $new_Id;
		}
		
		function getId()
		{
			return $this->id;
		}
		
		function setName($new_name)
		{
			$this->name = (string) $new_name;
		}
		
		function getName()
		{
			return $this->name;
		}
		
		function setDescription($new_description)
        {
            $this->description = (string) $new_description;
        }
		
		function getDescription()
		{
			return $this->description;
		}
		
		function setDatatype($new_datatype)
		{
			$this->datatype = $new_datatype;
		}
		
		function getDatatype()
		{
			return $this->datatype;
		}
		
		function setDescr($new_descr)
		{
			$this->descr = (string) $new_descr;
		}
		
		function getDescr()
		{
			return $this->descr;
		}
		
		/**
		* Uses the getAll method and then loops over array of properties
		*
		* @param $search_id integer
		* @return Property[] properties with given id
		*/
		static function findById($search_id)
		{
			$found_prop = null;
			$properties = Property::getAll();
			foreach($properties as $p) {
				$prop_id = $p->getId();
				if($prop_id == $search_id) {
					$found_prop = $p;
				}
			}
			return $found_prop;
		}
		
		/**
		* Executes a db query to get the properties with given name
		*
		* @param $search_name string
		* @return Property[]
		*/
		static function findByName($search_name)
		{
			$returned_props = $GLOBALS['DB']->query("SELECT * FROM properties WHERE name='" .$search_name ."';");
			
			$props = array();
			foreach ($returned_props as $p) {
				$id = $p['id'];
				$name = $p['name'];
				$description = $p['description'];
				$datatype = $p['datatype'];
				$descr = $p['descr'];
				$new_prop = new Property($id, $name, $description, $datatype, $descr);
				array_push($props, $new_prop);
			}
			
			return $props;
		}
		
		/**
		* Executes specific db query to get properties with given datatype
		*
		* @param $search_type string
		* @return Property[]
		*/
		static function findByType($search_type)
		{
			$returned_props = $GLOBALS['DB']->query("SELECT * FROM properties WHERE datatype='" .$search_type ."';");
			
			$props = array();
			foreach ($returned_props as $p) {
				$id = $p['id'];
				$name = $p['name'];
				$description = $p['description'];
				$datatype = $p['datatype'];
				$descr = $p['descr'];
				$new_prop = new Property($id, $name, $description, $datatype, $descr);
				array_push($props, $new_prop);
			}
			
			return $props;
		}
		
		/**
		* Stores the property object to the database
		* 
		*/
		function save()
		{
			$statement = $GLOBALS['DB']->query("INSERT INTO properties(name, description, datatype) VALUES ('{$this->getName()}','{$this->getDescription()}','{$this->getDatatype()}') RETURNING id, descr;");
			$result = $statements->fetch(PDO::FETCH_ASSOC);
			$this->setId($result['id']);
			$this->setDescr($result['descr']);
		}
		
		/**
		* Returns all the properties stored in the db 
		*
		* @return Property[]
		*/
		static function getAll()
		{
			$returned_props = $GLOBALS['DB']->query("SELECT * FROM properties ORDER BY id;");
			$props = array();
			foreach ($returned_props as $p) {
				$id = $p['id'];
				$name = $p['name'];
				$description = $p['description'];
				$datatype = $p['datatype'];
				$descr = $p['descr'];
				$new_prop = new Property($id, $name, $description, $datatype, $descr);
				array_push($props, $new_prop);
			}
			return $props;	
		}
	}
		

?>