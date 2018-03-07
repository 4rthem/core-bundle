<?php

namespace Arthem\Bundle\CoreBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressAutoCompleteType extends GoogleAutoCompleteType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setDataMapper($this)
            ->add('addressLine1', null, [
                'required' => $options['required'],
                'label' => 'form.address.addressLine1',
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('addressLine2', null, [
                'required' => false,
                'label' => 'form.address.addressLine2',
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'form.address.postalCode',
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'form.address.city',
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('region', TextType::class, [
                'label' => 'form.address.region',
                'attr' => [
                    'placeholder' => false,
                ],
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'placeholder' => 'form.select_placeholder',
                'label' => 'form.address.country',
            ])
            ->add('inputAddress', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class);
    }

    public function getBlockPrefix()
    {
        return 'address_autocomplete';
    }
}
