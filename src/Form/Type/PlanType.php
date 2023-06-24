<?php

    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    class PlanType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $option): void
        {
            $builder->add("name", TextType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("bgColor", TextType::class, ['attr' => ['maxlength' => 7]]);
            $builder->add("submit", SubmitType::class);
        }
    }

?>