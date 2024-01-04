import FormController from './form-controller.js';
import CustomContentLoaderController from './custom-loader.js';
import { redirectToTargetOrDefault } from '../utils.js'

class FormControllerWithLoader extends FormController {
    constructor(formElement, url, showAllErrors = false) {
        super(formElement, url, showAllErrors);

        this.loader = new CustomContentLoaderController();
    }
    beforeSend() {
        this.loader.show();
    }

    handleResponse(data) {
        this.loader.completeLoadingAsync().then(() => {
            if (data.data?.redirect_url) {
                window.location.href = data.data.redirect_url;
                return;
            }
            redirectToTargetOrDefault()
        })
    }

    handleError(error) {
        setTimeout(() => {
            this.loader.hide();
            this.submited()
        }, 1000)
    }
}

export default FormControllerWithLoader;
