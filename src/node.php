<?php 
	class Node 
	{
		private $id;
		private $name;
		private $description;
		private $descr;
		private $relations;
		
        function __construct($id = null, $name, $description, $descr=null)
        {
            $this->name = $name;
            $this->description = $description;
			$this->id = $id;
			$this->descr = $descr;
			$this->relations = array();
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
		
		function setDescr($new_descr)
		{
			$this->descr = (string) $new_descr;
		}
		
		function getDescr()
		{
			return $this->descr;
		}
		
		public function getRelations()
		{
			return $this->relations;
		}
		
		public function addRelation(Relation $newRelation)
		{
			array_push($this->relations, $newRelation);
		}
		
		function removeRelation(Relation $oldRelation)
		{
			//to be completed
		}
		
		//here the getAll method is used and then loop over array of nodes
		static function findById($search_id)
		{
			$found_node = null;
			$nodes = Node::getAll();
			foreach($nodes as $node) {
				$node_id = $node->getId();
				if($node_id == $search_id) {
					$found_node = $node;
				}
			}
			return $found_node;
		}
		
		//here the other option is used by executing a db query and then create a new node
		static function findByName($search_name)
		{
			$returned_nodes = $GLOBALS['DB']->query("SELECT * FROM nodes WHERE name='" .$search_name ."';");
			
			$nodes = array();
			foreach ($returned_nodes as $node) {
				$id = $node['id'];
				$name = $node['name'];
				$description = $node['description'];
				$descr = $node['descr'];
				$new_node = new Node($id, $name, $description, $descr);
				array_push($nodes, $new_node);
			}
			
			return $nodes;
		}
		
		static function findByDescription($search_term)
		{
			$returned_nodes = $GLOBALS['DB']->query("SELECT * FROM nodes WHERE descr@@plainto_tsquery('english','" .$search_term ."');");
			
			$nodes = array();
			foreach ($returned_nodes as $node) {
				$id = $node['id'];
				$name = $node['name'];
				$description = $node['description'];
				$descr = $node['descr'];
				$new_node = new Node($id, $name, $description, $descr);
				array_push($nodes, $new_node);
			}
			
			return $nodes;
		}
		
		function save()
		{
			$statement = $GLOBALS['DB']->query("INSERT INTO nodes(name, description) VALUES ('{$this->getName()}','{$this->getDescription()}') RETURNING id, descr;");
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$this->setId($result['id']);
			$this->setDescr($result['descr']);		
			
			//save the relations
			foreach($this->relations as $rel){
				$rel->setStart($this->id);
				$rel->save();
			}
		}
		
		function update($new_name, $new_description)
		{
            $GLOBALS['DB']->exec("UPDATE nodes SET name = '{$new_name}', description = '{$new_description}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
			$this->setDescription($new_description);
        
		}
		
		//find all relations starting form this node
		function findRelations()
		{
			$relations = Relation::findByStart($this->id);
			return $relations;
			
		}
		
		//find all relations where this node is the value
		function findEndRelations()
		{
			$relations = Relation::findByValue($this->id);
			return $relations;
		}
		
		//find all nodes with geometric relation
		static function getAllGeoNodes()
		{
			$georelations = Relation::getGeometryRelations();			
			$returned_nodes = array();
			$stored_ids = array();
			foreach ($georelations as $rel){
				$node_id = $rel->getStart()->getId();
				if(!in_array($node_id, $stored_ids)){
					array_push($returned_nodes, Node::findById($node_id));
					array_push($stored_ids, $node_id);
				}				
			}
			return $returned_nodes;
		}
		
		function findHistory()
		{
			$returned_history = $GLOBALS['DB']->query("
				SELECT * 
				FROM nodes_logging 
				WHERE id=" .$this->getId() ."
				ORDER BY action_time;"
			);		
			return $returned_history;
		}
		
		static function getAll()
		{
			$returned_nodes = $GLOBALS['DB']->query("SELECT * FROM nodes ORDER BY id;");
			$nodes = array();
			foreach ($returned_nodes as $node) {
				$id = $node['id'];
				$name = $node['name'];
				$description = $node['description'];
				$descr = $node['descr'];
				$new_node = new Node($id, $name, $description, $descr);
				array_push($nodes, $new_node);
			}
			return $nodes;			
		}
	}
?>