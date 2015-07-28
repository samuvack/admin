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
		
		//here the getAll method is used and then loop over array of relations
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
		
		//here the other option is used by executing a db query and then create a new relation
		//the property attribute of the relation is here set to a property object
		//if the datatype of the property is node, the value is a node object
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
				$new_rel->setQualifier($qualifier);
				$new_rel->setRank($rank);
				array_push($relations, $new_rel);
			}
			
			return $relations;
		}
		
		//searches the statements table to find relations where value equals the $value_node
		//the property attribute of the relation is here set to a property object
		//the start attribute of the relation is here set to a node object
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
		
		//searches the statements with a property which has datatype geometry
		//the value of the statement is replaced by the text representation of the geom
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
		
		//returns all the relations stored in the database as relation objects
		//the property attribute of the relation is here set to a property object
		//the start attribute of the relation is set to a node object
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