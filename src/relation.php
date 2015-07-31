<?php 
	class Relation 
	{
		private $id;
		private $start_node;
		private $property;
		private $value;
		private $qualifier;
		private $rank;
		
        function __construct($id = null, $start_node, $property, $value, $qualifier=null, $rank=null)
        {
            $this->id = $id;
            $this->start_node = $start_node;
			$this->property = $property;
			$this->value = $value;
			$this->qualifier = $qualifier;
			$this->rank = $rank;
        }

		function setId($new_Id)
		{
			$this->id = (int) $new_Id;
		}
		
		function getId()
		{
			return $this->id;
		}
		
		function setStart($new_start)
		{
			$this->start_node = (int) $new_start;
		}
		
		function getStart()
		{
			return $this->start_node;
		}
		
		function setProperty($new_prop)
		{
			$this->property = $new_prop;
		}
		
		function getProperty()
		{
			return $this->property;
		}
		
		function setValue($new_value)
        {
            $this->value = (string) $new_value;
        }
		
		function getValue()
		{
			return $this->value;
		}
		
		function setQualifier($new_qualifier)
		{
			$this->qualifier = (string) $new_qualifier;
		}
		
		function getQualifier()
		{
			return $this->qualifier;
		}
		
		function setRank($new_rank)
		{
			$this->rank = (string) $new_rank;
		}
		
		function getRank()
		{
			return $this->rank;
		}
		
		/**
		* Uses getAll() and then loops over array of relations
		*
		* @param $search_id integer
		* @return Relation
		*/
		static function findById($search_id)
		{
			$found_rel = null;
			$relations = Relation::getAll();
			foreach($relations as $rel) {
				$rel_id = $rel->getId();
				if($rel_id == $search_id) {
					$found_rel = $rel;
				}
			}
			return $found_rel;
		}
		
		/**
		* Specific db query to search relations with given start node.
		* The property attribute of the relation is set to a Property object
		* The value attribute of the relation is set to a node object, if property datatype is node
		*
		* @param integer $start_node
		* @return Relation[]
		*/
		static function findByStart($start_node)
		{
			$returned_relations = $GLOBALS['DB']->query("SELECT * FROM statements WHERE startid=" .$start_node .";");
			
			$relations = array();
			foreach ($returned_relations as $rel) {
				$id = $rel['id'];
				$start = $rel['startid'];
				$prop_id = $rel['propertyname'];
				$property = PROPERTY::findById($prop_id);
				
				//change the value by a node object is datatype of property is node
				if($property->getDatatype() == 'node'){
					$value = NODE::findById($rel['value']);
				} else {
					$value = $rel['value'];
				}
				
				$qualifier = $rel['qualifier'];
				$rank =$rel['rank'];
				$new_rel = new Relation($id, $start, $property, $value, $qualifier, $rank);
				array_push($relations, $new_rel);
			}
			
			return $relations;
		}
		
		/**
		* Uses getAll() to find all relations where value equals the $value_node
		* The property attribute of the relation is here set to a property object
		* The start attribute of the relation is here set to a node object
		*
		* @param integer $value_node
		* @return Relation[]
		*/
		static function findByValue($value_node)
		{
			$found_relations = array();
			$relations = Relation::getAll();
			
			foreach($relations as $rel) {
				$rel_prop = $rel->getProperty();
				$prop_datatype = $rel_prop->getDatatype();
				$rel_value = $rel->getValue();
				if($prop_datatype == 'node' && $rel_value == $value_node) {
					array_push($found_relations, $rel);
				}
			}
			return $found_relations;
		}
		
		/**
		* Use specific db query to get relations with the given property-value
		* Value is case insensitive
		* 
		* @param integer $property_id
		* @param string $value
		* @return integer[] A array of start id's		
		*/
		static function findByPropertyValue($property_id, $value){
			$returned_relations = $GLOBALS['DB']->query("SELECT startid FROM statements WHERE propertyname=" .$property_id ." and lower(value)=lower('" .$value ."');");
			$starts = array();
			foreach($returned_relations as $r){
				array_push($starts, $r['startid']);
			}
			return $starts;
		}
		
		/**
		* Uses getAll() to get all statements with a geometry property
		* The value of statement is text representation
		*
		* @return Relation[]
		*/
		static function getGeometryRelations()
		{
			$found_relations = array();
			$relations = Relation::getAll();
			
			foreach($relations as $rel) {
				$rel_prop = $rel->getProperty();
				$prop_datatype = $rel_prop->getDatatype();
				$rel_value = $rel->getValue();
				if($prop_datatype == 'geometry') {
					$geom = $GLOBALS['DB']->query("SELECT st_astext(geom) as geom FROM geometries WHERE id=" .$rel_value .";");
					$result = $geom->fetch(PDO::FETCH_ASSOC);
					$geom_text = $result['geom'];
					$rel->setValue($geom_text);
					array_push($found_relations, $rel);
				}
			}
			return $found_relations;
		}
		
		/**
		* Saves the relation object to the database
		*
		*/
		function save()
		{
			
			if($this->getRank()){
				$rank = (string) "'" .$this->getRank() ."'";
			}else{
				$rank = (string) 'null';
			}
			
			if ($this->getQualifier()){
				$qualifier = (string) "'" .$this->getQualifier() ."'";
			}else{
				$qualifier = (string) 'null';
			}
			
			$statement = $GLOBALS['DB']->query("
				INSERT INTO statements(startid, propertyname, value, qualifier, rank) 
				VALUES ({$this->getStart()},{$this->getProperty()},'{$this->getValue()}', $qualifier, $rank)
				RETURNING id;");
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$this->setId($result['id']);
		}
		
		/**
		* Updates the object and updates the database
		*
		* @param string $new_name
		* @param string $new_value
		* @param string $new_rank
		* @param integer $new_qualifier
		*/
		function update($new_name, $new_value, $new_rank, $new_qualifier)
		{
            $GLOBALS['DB']->exec("UPDATE nodes SET propertyname = '{$new_name}', value = '{$new_value}', rank = '{$new_rank}', qualifier = '{$new_qualifier}'  WHERE id = {$this->getId()};");
            $this->setProperty($new_name);
			$this->setValue($new_value);
			$this->setQualifier($new_qualifier);
			$this->setRank($new_rank);
        
		}
		
		/**
		* Returns all the relations stored in the database as relation objects
		* The property attribute of the relation is here set to a property object
		* The start attribute of the relation is set to a node object
		*
		* @retun Relation[]
		*/
		static function getAll()
		{
			$returned_relations = $GLOBALS['DB']->query("SELECT * FROM statements ORDER BY id;");
			$relations = array();
			foreach ($returned_relations as $rel) {
				$id = $rel['id'];
				$start_id = $rel['startid'];
				$start = NODE::findById($start_id);
				
				$property_id = $rel['propertyname'];
				$property = PROPERTY::findById($property_id);
				$value = $rel['value'];
				$qualifier = $rel['qualifier'];
				$rank =$rel['rank'];
				$new_rel = new Relation($id, $start, $property, $value, $qualifier, $rank);
				array_push($relations, $new_rel);
			}
			return $relations;			
		}
	}
?>