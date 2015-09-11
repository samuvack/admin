<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 09/09/15
 * Time: 09:39
 */

namespace MyApp\FormTypes;


use MyApp\Entities\ShadowEntities\Filter;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterType extends RelationType {
	protected function renderFormType($form, $type) {
		$formType = $this->app['mapping.manager']->getFormType($type);
		$form->add('value', $formType)
			->add('send', 'submit', array(
			'attr' => array('class' => 'btn btn-default')
		));
	}

	protected function renderSubRelations($form) {
		// do absolutely nothing
	}


	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
		    'data_class' => 'MyApp\Entities\ShadowEntities\Filter',
			'empty_data' => new Filter()
		));
	}

	public function getName() {
		return 'filter';
	}

	public function onPostSetData(FormEvent $event) {
		$form = $event->getForm();

		$type = 'text';
		if(sizeof($event->getData())>0) {
			$data = $event->getData();
			$type = $data->getType();
		}
		$this->renderFormType($form,$type);
		/*$form = $event->getForm();
		$type = 'text';
		if($event->getData()['type'] !== '') {
			print_r($event->getData());die();
			$data = $event->getData();
			$type = $event->getData()->getProperty()->getDataType();
		}
		$this->renderFormType($form,$type);*/
	}
}
