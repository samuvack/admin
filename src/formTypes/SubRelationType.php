<?php

namespace MyApp\FormTypes;


use MyApp\Entities\SecondaryRelation;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubRelationType extends RelationType {

	protected function renderSubRelations($form) {
		// do absolutely nothing
	}


	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'MyApp\Entities\SecondaryRelation',
			'empty_data' => new SecondaryRelation()
		));
	}

	public function getName() {
		return 'subrelation';
	}
}
