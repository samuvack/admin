<?php

namespace MyApp\FormTypes;


use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LinkType extends TextType {
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'csrf_protection' => false,
			'data_class' => 'MyApp\Values\LinkValue',
			'empty_data' => new \MyApp\Values\LinkValue('')
		));
	}

}
