import InputField from './InputController.js';

class FormController {
    constructor(formElement, url, showAllErrors = false) {
        this.form = formElement;
        this.url = url;
        this.showAllErrors = showAllErrors;
        this.generalOutput = this.form.querySelector('.output');
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
        if (!error.response) {
            this.showOutput('Błąd sieciowy lub inny błąd', true);
            this.handleError(error);
            return;
        }

        const contentType = error.response.headers.get("Content-Type");
        if (contentType && contentType.includes("application/json")) {
            error.response.json().then(data => {
                this.#handleJsonError(data, error.status);
            }).catch(jsonError => {
                console.error('Błąd podczas parsowania odpowiedzi JSON', jsonError);
            });
        } else {
            this.#handleErrorStatusCode(error.status);
        }

        this.handleError(error);
        this.finally();
    }

    #handleErrorStatusCode(statusCode) {
        switch (statusCode) {
            case 400:
                this.showOutput('Błąd w formularzu', true);
                break;
            case 401:
                this.showOutput('Nie jesteś zalogowany', true);
                break;
            case 409:
                this.showOutput('Nie masz uprawnień', true);
                break;
            case 404:
                this.showOutput('Strona nie istnieje', true);
                break;
            case 413:
                this.showOutput('Plik, który próbujesz przesłać ma zbyt duży rozmiar', true);
                break;
            default:
                this.showOutput(`Nieobsługiwany kod błędu: ${statusCode}`, true);
        }
    }

    #handleJsonError(data, statusCode) {
        switch (statusCode) {
            case 409:
            case 400:
                if (data.data.error) {
                    this.showOutput(data.data.error, true);
                }
                if (data.data.invalidFields) {
                    for (const [key, message] of Object.entries(data.data.invalidFields)) {
                        if (this.inputs[key]) {
                            this.inputs[key].showError(message);
                        }
                    }
                }
                break;
            case 401:
                this.showOutput(data.error, true);
                if (data.data.redirect_url) {
                    window.location.href = data.data.redirect_url;
                }
                break;
            default:
                console.log(`Nieobsługiwany kod statusu: ${statusCode}`);
                break;
        }
    }

    sendRequest() {
        fetch(this.url, {
            method: 'POST',
            body: new FormData(this.form)
        })
            .then(response => response.ok ? response.json() : Promise.reject({ response, status: response.status }))
            .then(data => this.#handleResponse(data))
            .catch(error => this.#handleError(error));
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
