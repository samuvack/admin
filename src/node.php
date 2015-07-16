<?php 
	class Node 
	{
		private $id;
		private $name;
		private $description;
		private $descr;
		
        function __construct($id = null, $name, $description, $descr=null)
        {
            $this->name = $name;
            $this->description = $description;
			$this->id = $id;
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
		
		function setDescr($new_descr)
		{
			$this->descr = (string) $new_descr;
		}
		
		function getDescr()
		{
			return $this->descr;
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
		
		function save()
		{
			$statement = $GLOBALS['DB']->exec("INSERT INTO nodes(name, description) VALUES ('{$this->getName()}','{$this->getDescription()}') RETURNING id, descr;");
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$this->setId($result['id']);
			$this->setDescr($result['descr']);
		}
		
		//create a relation class?
		function findRelations()
		{
			$relations = $GLOBALS['DB']->query("
				SELECT s.id as sid,p.id as pid,p.name as pname, p.datatype as ptype, s.value as svalue, n.name as nstart
				FROM statements as s, properties as p, nodes as n
				WHERE s.startID = '{$this->getId()}' and s.propertyName = p.id and s.startID = n.id;
				");			
			return $relations;
			
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