<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 31/08/15
 * Time: 09:21
 */

namespace MyApp\FormTypes\Import;
namespace MyApp\FormTypes\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\formBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use MyApp\Entities\Property;
use Silex\Application;

class NodeType extends AbstractType {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function getName() {
		return "node";
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name_column','integer')
			->add('description_column','integer')
			->add('relations', 'collection', array(
			'type' => new ColumnType($this->app),
			'allow_add' => true,
			//'by_reference'=> true,
		));
	}


}
