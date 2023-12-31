class InputField {
    constructor(inputElement, validationStrategy) {
        this.inputElement = inputElement;
        this.validationStrategy = validationStrategy;

        let nextSibling = this.inputElement.nextElementSibling;
        if (nextSibling &&
            nextSibling.tagName === 'SPAN' &&
            nextSibling.classList.length === 0 &&
            nextSibling.innerHTML.trim() === '') {
            this.errorSpan = nextSibling;
        } else {
            this.errorSpan = document.createElement('span');
            this.errorSpan.classList.add('input-error');
            this.inputElement.parentNode.insertBefore(this.errorSpan, this.inputElement.nextSibling);
        }

        this.inputElement.addEventListener('input', (e) => {
            this.hideError();
        });
    }

    validate() {
        if (!this.validationStrategy.validate(this.inputElement.value)) {
            this.showError(this.validationStrategy.errorMessage);
            return false;
        }
        this.hideError();
        return true;
    }

    showError(message) {
        this.errorSpan.innerText = message;
        this.errorSpan.classList.add('visible');
        this.inputElement.classList.add('invalid');
    }

    hideError() {
        this.errorSpan.classList.remove('visible');
        this.inputElement.classList.remove('invalid');
    }
}

export default InputField;