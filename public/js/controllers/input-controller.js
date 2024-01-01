class InputField {
    constructor(inputElement, validationStrategy = null) {
        this.inputElement = inputElement;
        this.validationStrategy = validationStrategy;

        if (this.validationStrategy) {
            let nextSibling = this.inputElement.nextElementSibling;

            if (nextSibling && nextSibling.tagName != 'SPAN') {
                let findErrorSpan = document.querySelector(`form *:has(*[name=${this.inputElement.name}]) span.input-error`);
                if (!findErrorSpan) {
                    console.warn('No error span found');
                } else {
                    this.errorSpan = findErrorSpan;
                }
            }
            else if (nextSibling &&
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

            this.inputElement.addEventListener('change', (e) => {
                this.hideError();
            });

            this.scrollParent = this.inputElement.closest('.scroll-to')
        }
    }

    validate() {
        if (this.validationStrategy) {
            if (!this.validationStrategy.validate(this.inputElement.value)) {
                this.showError(this.validationStrategy.errorMessage);
                return false;
            }
            this.hideError();
        }
        return true;
    }

    showError(message) {
        if (this.errorSpan) {
            this.errorSpan.innerText = message;
            this.errorSpan.classList.add('visible');
            this.scrollParent?.scrollIntoView({ block: 'start', behavior: 'smooth' });
        }
        this.inputElement.classList.add('invalid');
    }

    hideError() {
        this.errorSpan?.classList.remove('visible');
        this.inputElement.classList.remove('invalid');
    }
}

export default InputField;