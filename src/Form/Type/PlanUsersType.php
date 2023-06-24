<?php

    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\FormBuilderInterface;

    class PlanUsersType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $option): void
        {
            $builder->add("email", EmailType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("submit", SubmitType::class);
        }
    }

?>