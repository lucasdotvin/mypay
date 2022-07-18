<?php

namespace App\Rules\Documents;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $numericCpf = $this->removeNotNumericChars($value);

        if (strlen($numericCpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{9}/', $numericCpf)) {
            return false;
        }

        $firstDigit = $this->calculateFirstVerificationDigit($numericCpf);
        $secondDigit = $this->calculateSecondVerificationDigit($numericCpf);
        $verificationPair = $firstDigit.$secondDigit;

        return str_ends_with($numericCpf, $verificationPair);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The validation error message.';
    }

    private function removeNotNumericChars(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    private function calculateFirstVerificationDigit(string $cpf): string
    {
        $firstSum = 0;
        $weight = 10;

        for ($digitIndex = 0; $digitIndex < 9; $digitIndex++) {
            $firstSum += $cpf[$digitIndex] * $weight;

            $weight--;
        }

        $remainder = $firstSum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }

    private function calculateSecondVerificationDigit(string $cpf): string
    {
        $secondSum = 0;
        $weight = 11;

        for ($digitIndex = 0; $digitIndex < 10; $digitIndex++) {
            $secondSum += $cpf[$digitIndex] * $weight;

            $weight--;
        }

        $remainder = $secondSum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
