<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TelecashConnectGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('store_id', TextType::class, [
                'label' => 'turbine.sylius_telecash_plugin.connect.store_id.label',
                'constraints' => [
                    new NotBlank([
                        'message' => 'turbine.sylius_telecash_plugin.connect.store_id.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('user_id', TextType::class, [
                'label' => 'turbine.sylius_telecash_plugin.connect.user_id.label',
                'constraints' => [
                    new NotBlank([
                        'message' => 'turbine.sylius_telecash_plugin.connect.user_id.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('shared_secret', TextType::class, [
                'label' => 'turbine.sylius_telecash_plugin.connect.shared_secret.label',
                'constraints' => [
                    new NotBlank([
                        'message' => 'turbine.sylius_telecash_plugin.shared_secret.connect.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('mode', ChoiceType::class, [
                'choices' => [
                    'turbine.sylius_telecash_plugin.connect.mode.payonly' => 'payonly',
                ],
                'label' => 'turbine.sylius_telecash_plugin.connect.mode.label',
            ])
            ->add('hash_algorithm', ChoiceType::class, [
                'choices' => [
                    'turbine.sylius_telecash_plugin.connect.hash_algorithm.sha256' => 'SHA256',
                    'turbine.sylius_telecash_plugin.connect.hash_algorithm.sha512' => 'SHA512',
                ],
                'label' => 'turbine.sylius_telecash_plugin.connect.hash_algorithm.label',
            ])
            ->add('sandbox', CheckboxType::class, [
                'label' => 'turbine.sylius_telecash_plugin.connect.sandbox.label',
            ]);
    }
}
