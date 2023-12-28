import InputField from './InputController.js';

class FormController {
    constructor(formElement, url, showAllErrors = false) {
        this.form = formElement;
        this.url = url;
        this.showAllErrors = showAllErrors;

        this.generalOutput = this.form.querySelector('.output');
        this.form.addEventListener('submit', (e) => this.#handleSubmit(e));
        this.inputs = {}
    }

    /**
     * Register a new input field with the form controller
     * @param {string} inputName - the name of the input field
     * @param {function} validationStrategy - a function that returns true if the input is valid, or an error message if it is not
     */
    registerInput(inputName, validationStrategy) {
        this.inputs[inputName] = new InputField(this.getInputByName(inputName), validationStrategy);
    }

    #handleSubmit(e) {
        e.preventDefault();
        this.hideOutput();
        if (this.validate()) {
            this.beforeSend();
            this.sendRequest();
        }
    }

    sendRequest() {
        fetch(this.url, {
            method: 'POST',
            body: new FormData(this.form)
        })
            .then(response => {
                if (!response.ok) {
                    throw { response: response, status: response.status };
                }
                return response.json();
            })
            .then(data => this.handleResponse(data))
            .catch(error => this._handleError(error));
    }

    _handleError(error) {
        if (!error.response) {
            this.showOutput('Błąd sieciowy lub inny błąd', true);
            return;
        }

        const statusCode = error.status;
        error.response.json().then(data => {
            switch (statusCode) {
                case 409:
                    if (data.data.invalidFields) {
                        for (const [key, message] of Object.entries(data.data.invalidFields)) {
                            if (this.inputs[key]) {
                                this.inputs[key].showError(message);
                            }
                        }
                    }
                    break;
                case 400:
                    this.showOutput(data.data.error, true);
                    break;
                default:
                    console.log(`Nieobsługiwany kod statusu: ${statusCode}`);
                    break;
            }
        }).catch(jsonError => {
            console.error('Błąd podczas parsowania odpowiedzi JSON', jsonError);
        });

        this.handleError(error);
    }

    getInputByName(inputName) {
        return this.form.querySelector(`input[name="${inputName}"]`);
    }

    showOutput(text, error = false) {
        if (!this.generalOutput) {
            return;
        }
        this.generalOutput.innerText = text;
        this.generalOutput.classList.add('visible');
        if (error) {
            this.generalOutput.classList.add('error');
        } else {
            this.generalOutput.classList.remove('error');
        }
    }

    hideOutput() {
        this.generalOutput.innerText = '';
        this.generalOutput.classList.remove(['visible', 'error']);
    }

    validate() {
        let isValid = true;

        for (const key in this.inputs) {
            if (this.inputs.hasOwnProperty(key)) {
                if (!this.inputs[key].validate()) {
                    isValid = false;
                    if (!this.showAllErrors) {
                        break;
                    }
                }
            }
        }

        return isValid;
    }

    handleResponse(data) {
        throw new Error('Handle response from server not implemented');
    }

    handleError(error) {
        throw new Error('Handle error not implemented');
    }

    beforeSend() { }
}

export default FormController;