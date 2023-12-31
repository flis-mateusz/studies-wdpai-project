import FormController from './FormController.js';
import CustomContentLoaderController from './CustomContentLoaderController.js';

class FormControllerWithLoader extends FormController {
    constructor(formElement, url, showAllErrors = false) {
        super(formElement, url, showAllErrors);

        this.loader = new CustomContentLoaderController();
    }
    beforeSend() {
        this.loader.show();
    }

    handleResponse(data) {
        this.loader.completeLoadingAsync().then(redirectToTargetOrDefault)
    }

    handleError(error) {
        setTimeout(() => {
            this.loader.hide();
        }, 1000)
    }
}

export default FormControllerWithLoader;
