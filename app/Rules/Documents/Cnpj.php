<?php

namespace App\Rules\Documents;

use Illuminate\Contracts\Validation\Rule;

class Cnpj implements Rule
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
        $numericCnpj = $this->removeNotNumericChars($value);

        if (strlen($numericCnpj) != 14) {
            return false;
        }

        if (preg_match('/(\d)\1{9}/', $numericCnpj)) {
            return false;
        }

        $firstDigit = $this->calculateFirstVerificationDigit($numericCnpj);
        $secondDigit = $this->calculateSecondVerificationDigit($numericCnpj);
        $verificationPair = $firstDigit . $secondDigit;

        return str_ends_with($numericCnpj, $verificationPair);
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

    private function removeNotNumericChars(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    private function calculateFirstVerificationDigit(string $cnpj): string
    {
        $firstSum = 0;
        $weight = 6;

        for ($digitIndex = 0; $digitIndex < 12; $digitIndex++) {
            $firstSum += $cnpj[$digitIndex] * $weight;

            $weight++;

            if ($weight > 9) {
                $weight = 2;
            }
        }

        return $firstSum % 11 % 10;
    }

    private function calculateSecondVerificationDigit(string $cnpj): string
    {
        $secondSum = 0;
        $weight = 5;

        for ($digitIndex = 0; $digitIndex < 13; $digitIndex++) {
            $secondSum += $cnpj[$digitIndex] * $weight;

            $weight++;

            if ($weight > 9) {
                $weight = 2;
            }
        }

        return $secondSum % 11 % 10;
    }
}
