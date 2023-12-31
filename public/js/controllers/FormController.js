import InputField from './InputController.js';
import { FetchController, TextualError, JsonObjectError } from './FetchController.js';

class FormController {
    constructor(formElement, url, showAllErrors = false) {
        this.form = formElement;
        this.fetchController = new FetchController(url);
        this.showAllErrors = showAllErrors;
        this.generalOutput = this.form.querySelector('.form-output');
        this.form.addEventListener('submit', (e) => this.#handleSubmit(e));
        this.inputs = {};
    }

    registerInput(inputName, validationStrategy) {
        this.inputs[inputName] = new InputField(this.getInputByName(inputName), validationStrategy);
    }

    submited() {
        this.form.classList.remove('submitting');
    }

    #submitting() {
        this.form.classList.add('submitting');
    }

    #handleSubmit(e) {
        e.preventDefault();
        this.hideOutput();
        if (this.validate()) {
            this.beforeSend();
            this.#submitting();
            setTimeout(() => {
                this.sendRequest();
            }, 300);
        } else {
            this.finally();
        }
    }

    #handleResponse(data) {
        if (data.error) {
            this.showOutput(data.error, true);
            this.handleError(data);
        } else {
            this.handleResponse(data);
        }
        this.finally();
    }

    #handleError(error) {
        console.warn(error)
        if (!error.response) {
            this.showOutput(error.message, true);
            this.handleError(error)
            return;
        }

        if (error.response.error) {
            this.showOutput(error.response.error, true);
        }
        const responseData = error.response.data;
        console.log(error.response)
        if (responseData?.invalidFields) {
            for (const [key, message] of Object.entries(responseData.invalidFields)) {
                if (this.inputs[key]) {
                    this.inputs[key].showError(message);
                    if (!this.showAllErrors) {
                        break;
                    }
                }
            }
        }
        this.handleError(error)
    }
    

    async sendRequest() {
        await this.fetchController.post(new FormData(this.form))
            .then(data => {
                this.#handleResponse(data)
            })
            .catch(error => {
                this.#handleError(error);
            });
        this.finally();
    }

    getInputByName(inputName) {
        return this.form.querySelector(`input[name="${inputName}"]`);
    }

    showOutput(text, error = false) {
        if (!this.generalOutput) return;
        this.generalOutput.innerText = text;
        this.generalOutput.classList.add('visible');
        this.generalOutput.classList.toggle('error', error);
    }

    hideOutput() {
        this.generalOutput.innerText = '';
        this.generalOutput.classList.remove('visible', 'error');
    }

    validate() {
        let isValid = true;
        for (const key of Object.keys(this.inputs)) {
            if (!this.inputs[key].validate()) {
                isValid = false;
                if (!this.showAllErrors) {
                    break;
                }
            }
        }
        return isValid;
    }


    // Methods to be overridden in derived classes
    handleResponse(data) { }
    handleError(error) { }
    beforeSend() { }
    finally() { } // Finally method for cleanup or final actions
}

export default FormController;
