import CustomContentLoaderController from '../custom-loader.js';
import GalleryTransformerController from '../gallery-transformer.js';

class AdminUsers {
    constructor() {
        this.loader = new CustomContentLoaderController();
        this.loader.setupAbsoluteCenteredPOV()
        this.output = document.querySelector('span.input-error');
        this.referrer = document.referrer;

        document.querySelectorAll('div.user').forEach(userElement => {
            new GalleryTransformerController(userElement.querySelector('.avatar'))
        })
    }
}

new AdminUsers();