<?php

namespace App\Validator;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate($message): void
    {
        $errors = $this->validator->validate($message);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            throw new ValidationException($errorMessages);
        }
    }
}