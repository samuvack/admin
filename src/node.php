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
		
		/**
		* Adds the relation to the attribute relations
		*
		* @param $Relation newRelation the relation to be added
		*/
		public function addRelation(Relation $newRelation)
		{
			array_push($this->relations, $newRelation);
		}
		
		/**
		* Removes the given relation from the relations attribute
		*
		* @param Relation $oldRelation
		*/
		function removeRelation(Relation $oldRelation)
		{
			//to be completed
		}
		
		/**
		* getAll() is used, then loop over array of nodes
		*
		* @param integer $search_id
		* @return Node node with given id
		*/
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
			//This will store the relations starting at this node in the relations property
			//$found_node->findRelations();
			return $found_node;
		}
		
		/**
		* db query to find node and then create a new node
		*
		* @param string $search_name
		* @return Nodes[]
 		*/
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
		
		/**
		*
		* @param string $search_term
		* @return Nodes[]
		*/
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
		
		/**
		*get all the nodes with corresponding statements
		*
		* @param integer $prop_id
		* @param string $rel_value
		* @return Node[]
		*/
		static function findByPropertyValue($prop_id, $rel_value){
			$returned_nodes = Relation::findByPropertyValue($prop_id, $rel_value);
			$nodes = array();
			foreach ($returned_nodes as $n_id) {
				$new_node = Node::findById($n_id);
				array_push($nodes, $new_node);
			}
			return $nodes;
		}
		
		/**
		*Saves a node to the database as well as all its relations
		*/
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
		
		/**
		*
		* @param string $new_name
		* @param string $new_description
		*/
		function update($new_name, $new_description)
		{
            $GLOBALS['DB']->exec("UPDATE nodes SET name = '{$new_name}', description = '{$new_description}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
			$this->setDescription($new_description);
        
		}
		
		/**
		* Gives all the relations which start form this node
		*
		* @return Relations[]
		*/
		function findRelations()
		{
			$relations = Relation::findByStart($this->id);
			//the relations will be stored in the property relations
			//$this->relations = $relations;
			return $relations;
			
		}
		
		/**
		* Gives all the relations where this node is the value
		*
		* @return Relations[]
		*/
		function findEndRelations()
		{
			$relations = Relation::findByValue($this->id);
			return $relations;
		}
		
		/**
		* Gives all the nodes with a geometric relation
		*
		* @return Nodes[]
		*/
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
		
		/**
		* Gives the history of the node
		*
		* @return string[] database rows of nodes_logging
		*/
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
		
		/**
		* Returns all the nodes stored in the database
		*
		* @return Node[]
		*/
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