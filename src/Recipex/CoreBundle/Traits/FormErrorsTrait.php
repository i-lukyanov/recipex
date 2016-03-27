<?php
/**
 * Author: DezzmonD
 * Date: 13.03.2016
 */

namespace Recipex\CoreBundle\Traits;

use Symfony\Component\Form\FormInterface;

trait FormErrorsTrait
{
    /**
     * Возврат ошибок валидации из формы в виде массива
     *
     * @param FormInterface $form
     * @return array
     */
    public function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}