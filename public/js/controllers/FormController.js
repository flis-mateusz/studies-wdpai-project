class FormController {
    constructor(formElement, url) {
        this.form = formElement;
        this.url = url;
        this.output = this.form.querySelector('.output');
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => this.#handleSubmit(e));

        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.classList.remove('invalid')
                this.hideOutput()
            });
        });
    }

    showOutput(text, error = false) {
        if (!this.output) {
            return;
        }
        this.output.innerText = text;
        this.output.classList.add('visible');
        if (error) {
            this.output.classList.add('error');
        } else {
            this.output.classList.remove('error');
        }
    }

    hideOutput() {
        this.output.innerText = '';
        this.output.classList.remove(['visible', 'error']);
    }

    validate(formData) { return false; }

    beforeSend() { }

    #handleSubmit(e) {
        e.preventDefault();
        this.hideOutput();
        if (this.validate(new FormData(this.form))) {
            this.beforeSend();
            this.sendData();
        }
    }

    handleError(error) {
        console.log(error);
    }

    sendData() {
        const formData = new FormData(this.form);
        fetch(this.url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => this.handleResponse(data))
            .catch(error => this.handleError(error));
    }

    handleResponse(data) { }

    markInput(inputName, invalid = false) {
        const input = this.form.querySelector(`input[name="${inputName}"]`);
        if (invalid) {
            input.classList.add('invalid');
        } else {
            input.classList.remove('invalid');
        }
    }
}

export default FormController;