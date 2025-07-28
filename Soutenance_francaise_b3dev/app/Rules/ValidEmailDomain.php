<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier que c'est un email valide
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Le format de l\'email n\'est pas valide.');
            return;
        }

        // Extraire le domaine après l'arobase
        $domain = substr(strrchr($value, '@'), 1);

        // Vérifier que le domaine est autorisé
        $allowedDomains = ['ifran.ci', 'ifran.com', 'gmail.com', 'icloud.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];

        if (!in_array($domain, $allowedDomains)) {
            $fail('L\'email doit utiliser un domaine autorisé : @ifran.ci, @ifran.com, @gmail.com, @icloud.com, @yahoo.com, @hotmail.com, ou @outlook.com');
            return;
        }

        // Vérifier qu'il n'y a pas de chiffres après l'arobase
        $afterAt = substr($value, strpos($value, '@') + 1);
        if (preg_match('/\d/', $afterAt)) {
            $fail('L\'email ne peut pas contenir de chiffres après l\'arobase (@).');
            return;
        }
    }
}
