import { isNotEmpty, isEmpty, isEmail, isTwoOrMoreWords, arePasswordsSame, isPasswordStrong, isPhoneNumber, isNumber } from '../utils.js'

class ValidationStrategy {
    constructor(errorMessage) {
        this.errorMessage = errorMessage;
    }

    validate(value) {
        throw new Error('Metoda validate musi być zaimplementowana');
    }
}

class TwoOrMoreWordsValidation extends ValidationStrategy {
    validate(value) {
        return isTwoOrMoreWords(value);
    }
}

class EmailValidation extends ValidationStrategy {
    validate(value) {
        return isEmail(value);
    }
}

class NotEmptyValidation extends ValidationStrategy {
    validate(value) {
        return isNotEmpty(value);
    }
}

class PhoneNumberValidation extends ValidationStrategy {
    validate(value) {
        return isPhoneNumber(value);
    }
}

class NumberValidation extends ValidationStrategy {
    validate(value) {
        return isNumber(value);
    }
}

class InputMinLengthValidation extends ValidationStrategy {
    constructor(minLength, errorMessage) {
        super(errorMessage);
        this.minLength = minLength;
    }
    validate(value) {
        return value.length >= this.minLength;
    }
}

class PasswordValidation extends ValidationStrategy {
    constructor(skipEmpty = false, errorMessage = 'Hasło musi mieć co najmniej 8 znaków i zawierać duże i małe litery, cyfrę oraz znak specjalny') {
        super(errorMessage);
        this.skipEmpty = skipEmpty;
    }
    validate(value) {
        if (this.skipEmpty && isEmpty(value)) {
            return true;
        }
        return isPasswordStrong(value);
    }
}

class ArePasswordsSameValidation extends ValidationStrategy {
    constructor(errorMessage, otherInputField, allowEmpty = false) {
        super(errorMessage);
        this.otherInputField = otherInputField;
        this.allowEmpty = allowEmpty;
    }

    validate(value) {
        const otherValue = this.otherInputField.value;

        if (this.allowEmpty && isEmpty(value) && isEmpty(otherValue)) {
            return true;
        }

        return arePasswordsSame(value, otherValue);
    }
}

export {
    TwoOrMoreWordsValidation,
    EmailValidation,
    NotEmptyValidation,
    PasswordValidation,
    ArePasswordsSameValidation,
    PhoneNumberValidation,
    InputMinLengthValidation,
    NumberValidation
};